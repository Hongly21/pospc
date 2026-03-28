@extends('layouts.app')

@section('title', 'របាយការណ៍នៃលក់')
@section('content')
    <style>
        @media print {

            .navbar,
            .topbar,
            header,
            footer,
            .sidebar,
            .no-print {
                display: none !important;
            }

            .row.g-3 {
                display: flex !important;
                flex-wrap: nowrap !important;
                margin-bottom: 20px !important;
            }

            .col-xl-3 {
                width: 25% !important;
                flex: 0 0 25% !important;
                max-width: 25% !important;
                padding: 0 5px !important;
            }

            .card {
                border: 1px solid #000 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .card-body {
                padding: 10px !important;
            }

            .h4 {
                font-size: 14pt !important;
                margin: 0 !important;
            }

            .text-xs {
                font-size: 9pt !important;
                color: #000 !important;
            }

            .fa-2x {
                display: none !important;
            }


            table th:last-child,
            table td:last-child {
                display: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 20px !important;
            }

            table th,
            table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                font-size: 11pt !important;
                color: #000 !important;
            }

            body {
                background-color: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .badge {
                border: none !important;
                color: #000 !important;
                font-weight: bold !important;
                padding: 0 !important;
            }
        }
    </style>
    <div class="card shadow mb-4 border-0 no-print">
        <div class="card-body bg-light">
            <form action="{{ route('reports.sales') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">ចាប់ពី</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">រហូតដល់</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">អតិថិជន</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">ទាំងអស់ (All Customers)</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->CustomerID }}"
                                {{ $customerId == $cust->CustomerID ? 'selected' : '' }}>
                                {{ $cust->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">ស្ថានភាពបំណុល</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">ទាំងអស់ (All Status)</option>
                        <option value="Paid" {{ $statusFilter == 'Paid' ? 'selected' : '' }}>ទូទាត់រួច (Paid)</option>
                        <option value="Debt" {{ $statusFilter == 'Debt' ? 'selected' : '' }}>ជំពាក់ (Debt)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> ស្វែងរក
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ចំនួនលក់ (Orders)</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-info shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ប្រាក់លក់សរុប (Total Sales)
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ប្រាក់ទទួលបានជាក់ស្តែង
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalReceived, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-start border-4 border-danger shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ប្រាក់កំពុងជំពាក់ (Total
                                Debt)</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-clock fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">បញ្ជីប្រវត្តិលក់លម្អិត</h6>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print"><i class="fas fa-print"></i>
                បោះពុម្ព</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">INV #</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>អតិថិជន</th>
                            <th class="text-end">ប្រាក់សរុប</th>
                            <th class="text-end">បានបង់</th>
                            <th class="text-end">ជំពាក់</th>
                            <th class="text-center">ស្ថានភាព</th>
                            <th class="text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $paid = $order->receipts->sum('PaidAmount') - $order->receipts->sum('ChangeAmount');
                                $debt = max(0, $order->TotalAmount - $paid);
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold text-primary">#{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>{{ $order->created_at->format('d-M-Y H:i') }}</td>
                                <td class="fw-bold">{{ $order->customer->Name ?? 'អតិថិជនទូទៅ' }}</td>

                                <td class="text-end fw-bold">${{ number_format($order->TotalAmount, 2) }}</td>
                                <td class="text-end text-success">${{ number_format($paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $debt > 0 ? 'text-danger' : 'text-muted' }}">
                                    ${{ number_format($debt, 2) }}
                                </td>

                                <td class="text-center">
                                    @if ($order->Status == 'Paid')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success px-2">រួចរាល់</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2">នៅជំពាក់</span>
                                    @endif
                                </td>

                                <td class="text-center pe-3">
                                    <a href="{{ route('pos.receipt', $order->OrderID) }}" target="_blank"
                                        onclick="window.open(this.href, 'targetWindow', 'width=400,height=600'); return false;"
                                        class="btn btn-sm btn-light text-primary border shadow-sm">
                                        <i class="fas fa-print fa-sm me-2"></i> ព្រីនវិក័យប័ត្រ
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">មិនមានទិន្នន័យទេ!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end p-3 border-top">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
