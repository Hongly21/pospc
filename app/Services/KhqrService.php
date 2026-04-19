<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhqrService
{
    protected string $baseUrl;
    protected string $token;
    protected string $bakongAccount;
    protected string $merchantName;
    protected string $storeLabel;
    protected string $phone;

    public function __construct()
    {
        $this->baseUrl       = rtrim(config('khqr.base_url', 'https://api-bakong.nbc.gov.kh'), '/');
        $this->token         = config('khqr.token');
        $this->bakongAccount = config('khqr.account');       // e.g. hongly_boun@bkrt
        $this->merchantName  = config('khqr.merchant_name', 'My Shop');
        $this->storeLabel    = config('khqr.store_label', 'POS');
        $this->phone         = config('khqr.phone', '');
    }

    /**
     * Generate KHQR string locally — no API call needed.
     * Uses EMV QR standard that all Cambodian banking apps can scan.
     *
     * Returns ['success' => true, 'qr' => '...', 'md5' => '...']
     */
    public function generateQr(float $amount, string $currency = 'USD', string $billNumber = ''): array
    {
        try {
            if (empty($this->bakongAccount)) {
                return ['success' => false, 'message' => 'KHQR_ACCOUNT មិនបានកំណត់ក្នុង .env'];
            }

            $currencyCode = strtoupper($currency) === 'KHR' ? '116' : '840';
            $amountStr    = strtoupper($currency) === 'KHR'
                ? (string) intval($amount)
                : number_format($amount, 2, '.', '');

            if (empty($billNumber)) {
                $billNumber = 'INV' . time();
            }

            // Merchant account info (tag 29 for individual KHQR)
            // KHQR spec: sub-tag 00 = Bakong account ID directly
            $tag29Inner = $this->tlv('00', $this->bakongAccount);
            $tag29 = $this->tlv('29', $tag29Inner);

            // Timestamps for dynamic QR (milliseconds)
            $nowMs     = (int)(microtime(true) * 1000);
            $createdTs = (string) $nowMs;
            $expiredTs = (string) ($nowMs + 120000);               // expires in 2 minutes

            // Format phone to international 855 format
            $intlPhone = $this->phone;
            if (!empty($intlPhone)) {
                $intlPhone = ltrim($intlPhone, '+');
                if (str_starts_with($intlPhone, '0')) {
                    $intlPhone = '855' . substr($intlPhone, 1);
                }
            }

            // Additional data fields (tag 62)
            $tag62Inner  = $this->tlv('01', substr($billNumber, 0, 25));
            if (!empty($intlPhone)) {
                $tag62Inner .= $this->tlv('02', $intlPhone);                  // Mobile number (855...)
            }
            if (!empty($this->storeLabel)) {
                $tag62Inner .= $this->tlv('03', substr($this->storeLabel, 0, 25));
            }
            $tag62Inner .= $this->tlv('07', substr($this->storeLabel ?: 'POS', 0, 25)); // Terminal label
            $tag62  = $this->tlv('62', $tag62Inner);

            // Timestamp template — TOP-LEVEL tag 99 (NOT inside tag 62)
            $tag99Inner  = $this->tlv('00', $createdTs);
            $tag99Inner .= $this->tlv('01', $expiredTs);
            $tag99 = $this->tlv('99', $tag99Inner);

            $qrBody  = '000201';                                              // Payload format indicator
            $qrBody .= '010212';                                              // Dynamic QR
            $qrBody .= $tag29;                                                // Bakong account (tag 29)
            $qrBody .= $this->tlv('52', '5999');                              // MCC
            $qrBody .= $this->tlv('53', $currencyCode);                       // Currency
            $qrBody .= $this->tlv('54', $amountStr);                          // Amount
            $qrBody .= $this->tlv('58', 'KH');                                // Country
            $qrBody .= $this->tlv('59', substr($this->merchantName, 0, 25)); // Merchant name
            $qrBody .= $this->tlv('60', 'Phnom Penh');                        // City
            $qrBody .= $tag62;                                                // Additional data
            $qrBody .= $tag99;                                                // Timestamp template
            $qrBody .= '6304';                                                // CRC tag

            $crc    = $this->crc16($qrBody);
            $qrFull = $qrBody . strtoupper($crc);
            $md5    = md5($qrFull);

            return [
                'success' => true,
                'qr'      => $qrFull,
                'md5'     => $md5,
            ];
        } catch (\Exception $e) {
            Log::error('KHQR generateQr failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Poll Bakong API to check if the QR was paid (by MD5 hash).
     * Returns ['paid' => true/false]
     */
    public function checkPayment(string $md5): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(10)
                ->post("{$this->baseUrl}/v1/check_transaction_by_md5", [
                    'md5' => $md5,
                ]);

            Log::info('KHQR check response: ' . $response->body());

            if ($response->successful()) {
                $data       = $response->json();
                $statusCode = $data['responseCode'] ?? ($data['status']['code'] ?? -1);

                // Bakong: responseCode=0 AND non-null data = paid
                if ($statusCode === 0 && !empty($data['data'])) {
                    return ['paid' => true, 'data' => $data['data']];
                }

                return ['paid' => false];
            }

            return ['paid' => false, 'error' => 'API error ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('KHQR checkPayment failed: ' . $e->getMessage());
            return ['paid' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /** EMV TLV: Tag + zero-padded 2-digit Length + Value */
    private function tlv(string $tag, string $value): string
    {
        return $tag . str_pad(strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    /** CRC-16/CCITT-FALSE required by EMV QR spec */
    private function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return str_pad(strtoupper(dechex($crc)), 4, '0', STR_PAD_LEFT);
    }
}
