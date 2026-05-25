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
                <div>{{ $setting->shop_address ?? __('settings.address') }}</div>
                <div>{{ __('settings.phone') }}: {{ $setting->shop_phone ?? '000-000-000' }}</div>
            </div>
            <div class="col-right">
                <h2 class="invoice-title">{{ __('receipt.title') }}</h2>
                <div class="info-grid">
                    <span>{{ __('receipt.invoice_no') }}</span>
                    <span class="receipt-text-normal">{{ $latestReceipt->ReceiptNo ?? '-' }}</span>
                    <span>{{ __('receipt.order_date') }}</span>
                    <span class="receipt-text-normal">{{ $order->created_at->format('Y-m-d') }}</span>
                    <span>{{ __('receipt.time') }}</span>
                    <span class="receipt-text-normal">{{ $order->created_at->format('H:i') }}</span>
                    <span>{{ __('receipt.order_no') }}</span>
                    <span class="receipt-text-normal">#{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        {{-- ── Customer + Order Info (merged, no redundant meta table) ── --}}
        <div class="customer-section">
            <div class="customer-info">
                <div class="fw-bold text-uppercase receipt-section-title">{{ __('receipt.customer_info') }}</div>
                <div><strong>{{ __('receipt.name') }}</strong> {{ $order->customer->Name ?? __('general_customer') }}</div>
                @if ($order->customer)
                    <div><strong>{{ __('receipt.phone') }}</strong> {{ $order->customer->PhoneNumber }}</div>
                @endif
            </div>
            <div class="customer-info">
                <div class="fw-bold text-uppercase receipt-section-title">{{ __('receipt.order_info') }}</div>
                <div><strong>{{ __('receipt.seller') }}</strong> {{ $order->user->Username ?? 'Admin' }}</div>
                <div><strong>{{ __('receipt.payment_method') }}</strong> {{ $order->PaymentType }}</div>
                <div><strong>{{ __('receipt.status') }}</strong> <span
                        class="{{ $debt > 0 ? 'text-danger fw-bold' : '' }}">{{ $order->Status }}</span></div>
            </div>
        </div>

        {{-- ── Items Table (only actual items, no empty padding rows) ── --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="receipt-table-col-1">#</th>
                    <th class="receipt-table-col-2">{{ __('receipt.description') }}</th>
                    <th class="receipt-table-col-3">{{ __('receipt.qty') }}</th>
                    <th class="receipt-table-col-4">{{ __('receipt.unit_price') }}</th>
                    <th class="receipt-table-col-5">{{ __('receipt.tax_percent') }}</th>
                    <th class="receipt-table-col-6">{{ __('receipt.tax') }}</th>
                    <th class="receipt-table-col-7">{{ __('receipt.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->details as $index => $detail)
                    <tr class="{{ $loop->last ? 'last-row' : '' }}">
                        <td class="text-center receipt-cell-top">{{ $index + 1 }}</td>
                        <td class="text-start">
                            {{ $detail->product->Name }}

                        </td>
                        <td class="text-center">{{ $detail->Quantity }}</td>
                        <td class="text-center">${{ number_format($detail->unit_price, 2) }}</td>
                        <td class="text-center">{{ number_format($detail->tax_rate, 2) }}%</td>
                        <td class="text-end">${{ number_format($detail->tax_amount, 2) }}</td>
                        <td class="text-end">${{ number_format($detail->Subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $totalTax = $order->TotalTax && $order->TotalTax > 0 ? $order->TotalTax : $order->details->sum(fn($detail) => $detail->tax_amount);
            $subtotalWithoutTax = $order->TotalAmount - $totalTax;
        @endphp

        {{-- ── Totals ── --}}
        <div class="summary-container">
            <div class="notes-box">
                <div class="fw-bold text-uppercase receipt-meta-title">{{ __('receipt.notes') }}</div>
                <div class="notes-box-inner">
                    @if ($debt > 0)
                        <span class="text-danger fw-bold">{{ __('receipt.unpaid_warning') }}</span>
                    @else
                        <span>{{ __('receipt.paid_thanks') }}</span>
                    @endif
                </div>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="label">{{ __('receipt.subtotal') }}</td>
                    <td class="text-end">${{ number_format($subtotalWithoutTax, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('receipt.total_tax') }}</td>
                    <td class="text-end">${{ number_format($totalTax, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('receipt.total_amount') }}</td>
                    <td class="text-end">${{ number_format($order->TotalAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('receipt.total_paid') }}</td>
                    <td class="text-end">${{ number_format($actualPaidToBill, 2) }}</td>
                </tr>
                @if ($debt > 0)
                    <tr>
                        <td class="label text-danger">{{ __('receipt.remaining_debt') }}</td>
                        <td class="text-end fw-bold text-danger">${{ number_format($debt, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="label">{{ __('receipt.change') }}</td>
                        <td class="text-end">${{ number_format($totalChange, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- ── Payment History (shows each payment transaction) ── --}}
        @if ($order->receipts->count() > 0)
            <div class="payment-history">
                <div class="fw-bold text-uppercase receipt-meta-title">{{ __('receipt.payment_history') }}</div>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('receipt.receipt_no') }}</th>
                            <th>{{ __('receipt.method') }}</th>
                            <th>{{ __('receipt.date') }}</th>
                            <th class="text-end">{{ __('receipt.paid') }}</th>
                            <th class="text-end">{{ __('receipt.change_col') }}</th>
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
            {{ __('receipt.footer_thanks') }} <br>
            <span class="receipt-footer-note">{{ __('receipt.footer_thanks') }}</span>
        </div>

        <div class="text-center mt-2 no-print receipt-print-wrapper">
            <button onclick="window.print()" class="receipt-print-btn">
                {{ __('receipt.print_button') }}
            </button>
        </div>

    </div>

</body>

</html>
