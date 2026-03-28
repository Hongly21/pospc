<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->OrderID }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang&display=swap" rel="stylesheet">
    <style>
        /* A5 Print Settings */
        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        /* General Styling */
        body {
            font-family: 'Battambang', Arial, sans-serif;
            font-size: 11px;
            /* Scaled down for A5 */
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 10px;
        }

        .invoice-container {
            max-width: 148mm;
            /* A5 Width */
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            /* Reduced padding */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Layout */
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .col-left {
            width: 55%;
        }

        .col-left img {
            width: 70px;
            height: 70px;
        }

        .col-right {
            width: 42%;
            text-align: right;
        }

        /* Header Section */
        .header-logo {
            max-width: 100px;
            /* Smaller logo for A5 */
            max-height: 50px;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 16px;
            /* Scaled down */
            font-weight: bold;
            margin: 0 0 3px 0;
        }

        .invoice-title {
            font-size: 24px;
            /* Scaled down */
            font-weight: bold;
            color: #e0e6ed;
            letter-spacing: 1px;
            margin: 0 0 10px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: auto auto;
            gap: 3px 10px;
            justify-content: end;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        /* Customer Section */
        .customer-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .customer-info strong {
            display: inline-block;
            width: 70px;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        /* Meta Table */
        .meta-table th {
            background-color: #e6eef5;
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 10px;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 10px;
            height: 15px;
        }

        /* Items Table */
        .items-table th {
            background-color: #e6eef5;
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 5px;
        }

        .items-table tr.last-row td {
            border-bottom: 1px solid #000;
        }

        .items-table tbody tr {
            height: 20px;
        }

        /* Summary Section */
        .summary-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .notes-box {
            width: 48%;
        }

        .notes-box-inner {
            border: 1px dashed #000;
            padding: 8px;
            min-height: 50px;
            margin-top: 3px;
            font-size: 10px;
        }

        .totals-table {
            width: 48%;
        }

        .totals-table td {
            padding: 4px;
            border: 1px solid #000;
            font-size: 11px;
        }

        .totals-table td.label {
            text-align: right;
            font-weight: bold;
            border: none;
            padding-right: 10px;
        }

        /* Footer */
        .footer-thanks {
            background-color: #f2f2f2;
            text-align: center;
            padding: 8px;
            font-style: italic;
            font-weight: bold;
            margin-top: 20px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

{{-- Recipt in English --}}
{{-- <body>

    @php
        $totalPaid = $order->receipts->sum('PaidAmount');
        $totalChange = $order->receipts->sum('ChangeAmount');
        $actualPaidToBill = $totalPaid - $totalChange;
        $debt = max(0, $order->TotalAmount - $actualPaidToBill);
    @endphp

    <div class="invoice-container">

        <div class="row">
            <div class="col-left">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h1 class="company-name">{{ $setting->shop_name ?? 'POS System' }}</h1>
                <div>{{ $setting->shop_address ?? 'Address: Phnom Penh, Cambodia' }}</div>
                <div>Tel: {{ $setting->shop_phone ?? '000-000-000' }}</div>
            </div>
            <div class="col-right">
                <h2 class="invoice-title">INVOICE</h2>
                <div class="info-grid">
                    <span>DATE IN:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('Y-m-d') }}</span>
                    <span>TIME IN:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('H:i') }}</span>
                    <span>INVOICE #:</span>
                    <span style="font-weight: normal;">{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-info">
                <div class="fw-bold text-uppercase mb-1" style="margin-bottom: 3px;">Customer Information:</div>
                <div><strong>Name:</strong> {{ $order->customer->Name ?? 'អតិថិជនទូទៅ (General)' }}</div>
                @if ($order->customer)
                    <div><strong>Phone:</strong> {{ $order->customer->PhoneNumber }}</div>
                @endif
            </div>
            <div class="customer-info">
                <div class="fw-bold text-uppercase mb-1" style="margin-bottom: 3px;">Order Details:</div>
                <div><strong>Sales:</strong> {{ $order->user->Username ?? 'Admin' }}</div>
                <div><strong>Status:</strong> <span
                        class="{{ $debt > 0 ? 'text-danger fw-bold' : '' }}">{{ $order->Status }}</span></div>
            </div>
        </div>

        <table class="meta-table">
            <thead>
                <tr>
                    <th>PAYMENT METHOD</th>
                    <th>SALESPERSON</th>
                    <th>ORDER DATE</th>
                    <th>ORDER TIME</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->PaymentType }}</td>
                    <td>{{ $order->user->Username ?? 'Admin' }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ $order->created_at->format('H:i') }}</td>
                </tr>
            </tbody>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 50%; text-align: left;">Description (ទំនិញ)</th>
                    <th style="width: 15%;">Qty</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->details as $index => $detail)
                    <tr class="{{ $loop->last ? 'last-row' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-start">{{ $detail->product->Name }}</td>
                        <td class="text-center">{{ $detail->Quantity }}</td>
                        <td class="text-center">${{ number_format($detail->Subtotal / $detail->Quantity, 2) }}</td>
                        <td class="text-end">${{ number_format($detail->Subtotal, 2) }}</td>
                    </tr>
                @endforeach

                @for ($i = count($order->details); $i < 4; $i++)
                    <tr class="{{ $i == 3 ? 'last-row' : '' }}">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="summary-container">
            <div class="notes-box">
                <div class="fw-bold text-uppercase" style="font-size: 10px;">Customer Notes:</div>
                <div class="notes-box-inner">
                    @if ($debt > 0)
                        <span class="text-danger fw-bold">*** វិក្កយបត្រនេះមិនទាន់ទូទាត់គ្រប់ចំនួនទេ / Invoice Not Fully
                            Paid ***</span>
                    @endif
                </div>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="label">SUBTOTAL</td>
                    <td class="text-end">${{ number_format($order->TotalAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">PAID</td>
                    <td class="text-end">${{ number_format($totalPaid, 2) }}</td>
                </tr>

                @if ($debt > 0)
                    <tr>
                        <td class="label text-danger">TOTAL DUE</td>
                        <td class="text-end fw-bold text-danger">${{ number_format($debt, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="label">CHANGE</td>
                        <td class="text-end">${{ number_format($totalChange, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label fw-bold">TOTAL DUE</td>
                        <td class="text-end fw-bold">$0.00</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="footer-thanks">
            THANK YOU FOR YOUR BUSINESS! <br>
            <span style="font-weight: normal; font-size: 10px;">សូមអរគុណសម្រាប់ការទិញទំនិញជាមួយយើងខ្ញុំ!</span>
        </div>

        <div class="text-center mt-2 no-print" style="margin-top: 20px;">
            <button onclick="window.print()"
                style="padding: 8px 16px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                🖨️ Print Invoice (A5)
            </button>
        </div>

    </div>

</body> --}}

{{-- recipt in Khmer --}}

<body>

    @php
        $totalPaid = $order->receipts->sum('PaidAmount');
        $totalChange = $order->receipts->sum('ChangeAmount');
        $actualPaidToBill = $totalPaid - $totalChange;
        $debt = max(0, $order->TotalAmount - $actualPaidToBill);
    @endphp

    <div class="invoice-container">

        <div class="row">
            <div class="col-left">
                <img src="{{ asset('Uploads/products/TottaLogo.png') }}" alt="Logo">
                <h1 class="company-name">{{ $setting->shop_name ?? 'YOTTA PRINTER TECHNOLOGY' }}</h1>
                <div>{{ $setting->shop_address ?? 'អាសយដ្ឋាន: ភ្នំពេញ, កម្ពុជា' }}</div>
                <div>ទូរស័ព្ទ: {{ $setting->shop_phone ?? '000-000-000' }}</div>
            </div>
            <div class="col-right">
                <h2 class="invoice-title">វិក្កយបត្រ</h2>
                <div class="info-grid">
                    <span>កាលបរិច្ឆេទចូល:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('Y-m-d') }}</span>
                    <span>ម៉ោងចូល:</span>
                    <span style="font-weight: normal;">{{ $order->created_at->format('H:i') }}</span>
                    <span>លេខវិក្កយបត្រ:</span>
                    <span style="font-weight: normal;">{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-info">
                <div class="fw-bold text-uppercase mb-1" style="margin-bottom: 3px;">ព័ត៌មានអតិថិជន:</div>
                <div><strong>ឈ្មោះ:</strong> {{ $order->customer->Name ?? 'អតិថិជនទូទៅ' }}</div>
                @if ($order->customer)
                    <div><strong>ទូរស័ព្ទ:</strong> {{ $order->customer->PhoneNumber }}</div>
                @endif
            </div>
            <div class="customer-info">
                <div class="fw-bold text-uppercase mb-1" style="margin-bottom: 3px;">ព័ត៌មានការបញ្ជាទិញ:</div>
                <div><strong>អ្នកលក់:</strong> {{ $order->user->Username ?? 'Admin' }}</div>
                <div><strong>ស្ថានភាព:</strong> <span
                        class="{{ $debt > 0 ? 'text-danger fw-bold' : '' }}">{{ $order->Status }}</span></div>
            </div>
        </div>

        <table class="meta-table">
            <thead>
                <tr>
                    <th>វិធីសាស្រ្តបង់ប្រាក់</th>
                    <th>អ្នកលក់</th>
                    <th>កាលបរិច្ឆេទបញ្ជាទិញ</th>
                    <th>ម៉ោងបញ្ជាទិញ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->PaymentType }}</td>
                    <td>{{ $order->user->Username ?? 'Admin' }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ $order->created_at->format('H:i') }}</td>
                </tr>
            </tbody>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 50%; text-align: left;">ការពិពណ៌នា (ទំនិញ)</th>
                    <th style="width: 15%;">ចំនួន</th>
                    <th style="width: 15%;">តម្លៃឯកតា</th>
                    <th style="width: 15%;">សរុប</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->details as $index => $detail)
                    <tr class="{{ $loop->last ? 'last-row' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-start">{{ $detail->product->Name }}</td>
                        <td class="text-center">{{ $detail->Quantity }}</td>
                        <td class="text-center">${{ number_format($detail->Subtotal / $detail->Quantity, 2) }}</td>
                        <td class="text-end">${{ number_format($detail->Subtotal, 2) }}</td>
                    </tr>
                @endforeach

                @for ($i = count($order->details); $i < 4; $i++)
                    <tr class="{{ $i == 3 ? 'last-row' : '' }}">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="summary-container">
            <div class="notes-box">
                <div class="fw-bold text-uppercase" style="font-size: 10px;">កំណត់ចំណាំអតិថិជន:</div>
                <div class="notes-box-inner">
                    @if ($debt > 0)
                        <span class="text-danger fw-bold">*** វិក្កយបត្រនេះមិនទាន់បង់ប្រាក់ពេញលេញទេ ***</span>
                    @endif
                </div>
            </div>

            <table class="totals-table">
                <tr>
                    <td class="label">សរុបរង</td>
                    <td class="text-end">${{ number_format($order->TotalAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">បានបង់</td>
                    <td class="text-end">${{ number_format($totalPaid, 2) }}</td>
                </tr>

                @if ($debt > 0)
                    <tr>
                        <td class="label text-danger">ប្រាក់ជំពាក់សរុប</td>
                        <td class="text-end fw-bold text-danger">${{ number_format($debt, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="label">ប្រាក់អាប់</td>
                        <td class="text-end">${{ number_format($totalChange, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label fw-bold">ប្រាក់ជំពាក់សរុប</td>
                        <td class="text-end fw-bold">$0.00</td>
                    </tr>
                @endif
            </table>
        </div>

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
