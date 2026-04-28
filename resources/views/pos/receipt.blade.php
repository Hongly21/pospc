<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->OrderID }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/receipt.css') }}">
</head>

<body>

    @php
        $totalPaid = $order->receipts->sum('PaidAmount');
        $totalChange = $order->receipts->sum('ChangeAmount');
        $actualPaidToBill = $totalPaid - $totalChange;
        $debt = max(0, $order->TotalAmount - $actualPaidToBill);
        $latestReceipt = $order->receipts->last();
    @endphp

    <div class="invoice-container">

        {{-- ── Header: Logo + Shop Info | Invoice Title + Details ── --}}
        <div class="row">
            <div class="col-left">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
                <h1 class="company-name">{{ $setting->shop_name ?? 'YOTTA PRINTER TECHNOLOGY' }}</h1>
                <div>{{ $setting->shop_address ?? 'អាសយដ្ឋាន: ភ្នំពេញ, កម្ពុជា' }}</div>
                <div>ទូរស័ព្ទ: {{ $setting->shop_phone ?? '000-000-000' }}</div>
            </div>
            <div class="col-right">
                <h2 class="invoice-title">វិក្កយបត្រ</h2>
                <div class="info-grid">
                    <span>លេខបង្កាន់ដៃ:</span>
                    <span style="font-weight: normal;">{{ $latestReceipt->ReceiptNo ?? '-' }}</span>
                    <span>កាលបរិច្ឆេទបញ្ជាទិញ:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('Y-m-d') }}</span>
                    <span>ម៉ោង:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('H:i') }}</span>
                    <span>លេខបញ្ជាទិញ:</span>
                    <span style="font-weight: normal;">#{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        {{-- ── Customer + Order Info (merged, no redundant meta table) ── --}}
        <div class="customer-section">
            <div class="customer-info">
                <div class="fw-bold text-uppercase" style="margin-bottom: 3px;">ព័ត៌មានអតិថិជន:</div>
                <div><strong>ឈ្មោះ:</strong> {{ $order->customer->Name ?? 'អតិថិជនទូទៅ' }}</div>
                @if ($order->customer)
                    <div><strong>ទូរស័ព្ទ:</strong> {{ $order->customer->PhoneNumber }}</div>
                @endif
            </div>
            <div class="customer-info">
                <div class="fw-bold text-uppercase" style="margin-bottom: 3px;">ព័ត៌មានការបញ្ជាទិញ:</div>
                <div><strong>អ្នកលក់:</strong> {{ $order->user->Username ?? 'Admin' }}</div>
                <div><strong>វិធីបង់ប្រាក់:</strong> {{ $order->PaymentType }}</div>
                <div><strong>ស្ថានភាព:</strong> <span
                        class="{{ $debt > 0 ? 'text-danger fw-bold' : '' }}">{{ $order->Status }}</span></div>
            </div>
        </div>

        {{-- ── Items Table (only actual items, no empty padding rows) ── --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%; text-align: left;">ការពិពណ៌នា (ទំនិញ)</th>
                    <th style="width: 15%;">ចំនួន</th>
                    <th style="width: 17%;">តម្លៃឯកតា</th>
                    <th style="width: 18%;">សរុប</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->details as $index => $detail)
                    <tr class="{{ $loop->last ? 'last-row' : '' }}">
                        <td class="text-center" style="vertical-align: top;">{{ $index + 1 }}</td>
                        <td class="text-start">
                            {{ $detail->product->Name }}
                            @if ($detail->product && $detail->product->attributes->isNotEmpty())
                                <div style="font-size: 11px; color: #666;">{{ $detail->product->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->Quantity }}</td>
                        <td class="text-center">${{ number_format($detail->Quantity > 0 ? $detail->Subtotal / $detail->Quantity : 0, 2) }}</td>
                        <td class="text-end">${{ number_format($detail->Subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── Totals ── --}}
        <div class="summary-container">
            <div class="notes-box">
                <div class="fw-bold text-uppercase" style="font-size: 10px;">កំណត់ចំណាំ:</div>
                <div class="notes-box-inner">
                    @if ($debt > 0)
                        <span class="text-danger fw-bold">*** វិក្កយបត្រនេះមិនទាន់បង់ប្រាក់ពេញលេញទេ ***</span>
                    @else
                        <span>បានទូទាត់រួចរាល់។ សូមអរគុណ!</span>
                    @endif
                </div>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="label">សរុបរង</td>
                    <td class="text-end">${{ number_format($order->TotalAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">បានបង់សរុប</td>
                    <td class="text-end">${{ number_format($actualPaidToBill, 2) }}</td>
                </tr>
                @if ($debt > 0)
                    <tr>
                        <td class="label text-danger">ប្រាក់ជំពាក់នៅសល់</td>
                        <td class="text-end fw-bold text-danger">${{ number_format($debt, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="label">ប្រាក់អាប់</td>
                        <td class="text-end">${{ number_format($totalChange, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- ── Payment History (shows each payment transaction) ── --}}
        @if ($order->receipts->count() > 0)
            <div class="payment-history">
                <div class="fw-bold text-uppercase" style="font-size: 10px; margin-bottom: 5px;">ប្រវត្តិការបង់ប្រាក់:</div>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>លេខបង្កាន់ដៃ</th>
                            <th>វិធី</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>ប្រាក់បង់</th>
                            <th>ប្រាក់អាប់</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->receipts as $i => $receipt)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $receipt->ReceiptNo }}</td>
                                <td class="text-center">{{ $receipt->PaymentMethod }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($receipt->CreatedAt)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">${{ number_format($receipt->PaidAmount, 2) }}</td>
                                <td class="text-end">${{ number_format($receipt->ChangeAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer-thanks">
            សូមអរគុណសម្រាប់អាជីវកម្មរបស់អ្នក! <br>
            <span style="font-weight: normal; font-size: 10px;">សូមអរគុណសម្រាប់ការទិញទំនិញជាមួយយើង!</span>
        </div>

        <div class="text-center mt-2 no-print" style="margin-top: 20px;">
            <button onclick="window.print()"
                style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                បោះពុម្ពវិក្កយបត្រ
            </button>
        </div>

    </div>

</body>

</html>
