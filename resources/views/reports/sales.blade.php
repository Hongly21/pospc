@extends('layouts.app')

@section('title', __('sales_report_title'))
@section('content')
    <div class="card shadow mb-4 border-0 no-print">
        <div class="card-body">
            <form action="{{ route('reports.sales') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">{{ __('from_date') }}</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">{{ __('to_date') }}</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('customer') }}</label>
                    <select name="customer_id" class="form-select form-select-sm searchable-select">
                        <option value="">{{ __('all_customers') }}</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->CustomerID }}"
                                {{ $customerId == $cust->CustomerID ? 'selected' : '' }}>
                                {{ $cust->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('debt_status') }}</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('all_status') }}</option>
                        <option value="Paid" {{ $statusFilter == 'Paid' ? 'selected' : '' }}>{{ __('paid') }}
                        </option>
                        <option value="Debt" {{ $statusFilter == 'Debt' ? 'selected' : '' }}>{{ __('debt') }}
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary btn-sm ">{{ __('taxes.btn_search') }}</button>

                    {{-- reset button --}}

                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-secondary px-3 ms-2">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    <div class="row mb-4 g-3">
        {{-- Orders Count --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('order_count') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Sales --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('total_sales') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Received Amount --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('received_amount') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalReceived, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Debt --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-start shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('total_debt') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-danger">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-clock fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">{{ __('sales_history_detail') }}</h6>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print">
                <i class="fas fa-print"></i> {{ __('print') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">{{ __('inv_no') }}</th>
                            <th>{{ __('date') }}</th>
                            <th>{{ __('customer') }}</th>
                            <th class="text-end">{{ __('total_amount') }}</th>
                            <th class="text-end">{{ __('paid_amount') }}</th>
                            <th class="text-end">{{ __('debt_amount') }}</th>
                            <th class="text-center">{{ __('status') }}</th>
                            <th class="text-center pe-3">{{ __('actions') }}</th>
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
                                <td class="fw-bold">{{ $order->customer->Name ?? __('general_customer') }}</td>
                                <td class="text-end fw-bold">${{ number_format($order->TotalAmount, 2) }}</td>
                                <td class="text-end text-success">${{ number_format($paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $debt > 0 ? 'text-danger' : 'text-muted' }}">
                                    ${{ number_format($debt, 2) }}
                                </td>
                                <td class="text-center">
                                    @if ($order->Status == 'Paid')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success px-2">{{ __('paid_completed') }}</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2">{{ __('still_debt') }}</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <a href="{{ route('pos.receipt', $order->OrderID) }}" target="_blank"
                                        onclick="window.open(this.href, 'targetWindow', 'width=620,height=800'); return false;"
                                        class="btn btn-sm btn-light text-primary border shadow-sm">
                                        <i class="fas fa-print fa-sm "></i>
                                        {{-- {{ __('print_receipt') }} --}}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">{{ __('no_data_found') }}</td>
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            window.reportsSalesConfig = {
                selectPlaceholder: "{{ __('Select') }}"
            };
        </script>
        <script src="{{ asset('js/pages/reports-sales.js') }}"></script>
    @endpush
@endsection
