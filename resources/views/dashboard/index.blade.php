@extends('layouts.app')

@section('title', __('Dashboard Overview'))

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-white rounded-3">
                <div class="card-body d-flex flex-column flex-sm-row align-items-center justify-content-between py-3 px-4 gap-3">
                    <div>
                        <small class="text-muted">{{ __('during') }} <strong>{{ $rangeLabel }}</strong></small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-white border border-secondary-subtle dropdown-toggle rounded-pill px-4 py-2 fw-semibold text-secondary shadow-sm" type="button"
                            id="dashboardRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>{{ $periodOptions[$selectedPeriod] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3" aria-labelledby="dashboardRangeDropdown">
                            @foreach ($periodOptions as $value => $label)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center py-2 {{ $selectedPeriod === $value ? 'active bg-primary text-white' : '' }}"
                                        href="{{ route('dashboard', ['period' => $value, 'stock_filter' => $stockFilter]) }}">
                                        <span>{{ $label }}</span>
                                        @if ($selectedPeriod === $value)
                                            <i class="fas fa-check small {{ $selectedPeriod === $value ? 'text-white' : 'text-primary' }}"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-primary shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('Total Revenue') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-dark fw-bold">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                            <i class="fas fa-wallet fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-warning shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('Total Debt') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-dark fw-bold">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                            <i class="fas fa-hand-holding-usd fa-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-danger shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('General Expenses') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-dark fw-bold">-${{ number_format($totalExpenses, 2) }}</div>
                        </div>
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3">
                            <i class="fas fa-file-invoice-dollar fa-lg text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-secondary shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('cost_of_items_sold') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-dark fw-bold">-${{ number_format($cogs, 2) }}</div>
                        </div>
                        <div class="p-3 bg-secondary bg-opacity-10 rounded-3">
                            <i class="fas fa-boxes fa-lg text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-success shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('Estimated Profit') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-success fw-bold">${{ number_format($netProfit, 2) }}</div>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded-3">
                            <i class="fas fa-chart-line fa-lg text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 border-start border-4 border-info shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 fw-semibold tracking-wider">{{ __('Total_Orders') }}</div>
                            <div class="h3 mb-0 font-weight-bold text-dark fw-bold">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 rounded-3">
                            <i class="fas fa-shopping-cart fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100 bg-white rounded-3">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line me-2 text-danger"></i>{{ __('Top_Selling_Products') }}</h6>
                </div>
                <div class="card-body p-0">
                    @if ($topSellingProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>{{ __('Product') }}</th>
                                        <th class="text-end pe-4">{{ __('Sold') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="small text-dark">
                                    @foreach ($topSellingProducts as $index => $item)
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ optional($item->product)->Name ?? __('Unknown Product') }}</td>
                                            <td class="text-end pe-4"><span class="badge text-black bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3">{{ number_format($item->quantity_sold) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted small">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">{{ __('No sales data available yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100 bg-white rounded-3">
                <div class="card-header py-3 bg-white border-bottom border-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i>{{ __('Recent_Transactions') }}</h6>
                    <span class="badge bg-light text-dark border rounded-pill">{{ __('Last_5_orders') }}</span>
                </div>
                <div class="card-body p-0">
                    @if ($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">{{ __('Date') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th class="pe-4">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="small text-dark">
                                    @foreach ($recentTransactions as $transaction)
                                        <tr>
                                            <td class="ps-4 text-muted">{{ $transaction->OrderDate ? \Carbon\Carbon::parse($transaction->OrderDate)->format('d M Y H:i') : '' }}</td>
                                            <td class="fw-semibold">{{ optional($transaction->customer)->Name ?? __('Walk-in') }}</td>
                                            <td class="fw-bold">${{ number_format($transaction->TotalAmount, 2) }}</td>
                                            <td class="pe-4">
                                                @if($transaction->Status === 'Paid')
                                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">{{ __('Paid') }}</span>
                                                @elseif($transaction->Status === 'Partial')
                                                    <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">{{ __('Partial') }}</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">{{ __($transaction->Status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted small">
                            <i class="fas fa-receipt fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">{{ __('No recent transactions yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm bg-white rounded-3">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-bar me-2 text-primary"></i>{{ __('Weekly Revenue Graph') }}</h6>
                </div>
                <div class="card-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm bg-white rounded-3">
                <div class="card-header py-3 bg-white border-bottom border-light">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-pie-chart me-2 text-indigo"></i>{{ __('Payment Methods') }}</h6>
                </div>
                <div class="card-body">
                    <div style="height: 320px; position: relative;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3 bg-white mb-5">
        <div class="card-header py-3 bg-white border-bottom border-light d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <h6 class="m-0 fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Low/Out of Stock Items') }}</h6>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1 rounded-pill small">{{ $alertStockItems->total() }} {{ __('items') }}</span>
            </div>

            <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                <label for="stock_filter" class="small text-muted mb-0 text-nowrap fw-semibold">{{ __('Filter Condition:') }}</label>
                <select name="stock_filter" id="stock_filter" class="form-select form-select-sm border-secondary-subtle rounded-pill px-3 shadow-sm" style="min-width: 160px;" onchange="this.form.submit()">
                    <option value="all" {{ $stockFilter === 'all' ? 'selected' : '' }}>{{ __('All Alerts') }}</option>
                    <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                    <option value="out" {{ $stockFilter === 'out' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            @if ($alertStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th class="ps-4">{{ __('Product Name') }}</th>
                                <th>{{ __('Stock Left') }}</th>
                                <th>{{ __('inventory.reorder_level') }}</th>
                                <th class="text-center pe-4">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="small text-dark">
                            @foreach ($alertStockItems as $item)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $item->Name }}</td>
                                    <td>
                                        @if (($item->inventory->Quantity ?? 0) > 0)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-1.5 rounded-pill fw-bold">
                                                {{ $item->inventory->Quantity }} ({{ __('Low Stock') }})
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold">
                                                0 ({{ __('Out of Stock') }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted fw-semibold">{{ $item->inventory->ReorderLevel ?? 0 }} units</td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold transition-all shadow-sm">
                                            <i class="fas fa-plus me-1"></i> {{ __('Restock Now') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-center border-top border-light">
                    {!! $alertStockItems->links('pagination::bootstrap-5') !!}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                    <p class="text-muted mb-0 fw-semibold">{{ __('No entries found matching this alert condition!') }}</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            window.dashboardIndexConfig = {
                chartDates: {!! json_encode($chartDates) !!},
                chartSales: {!! json_encode($chartSales) !!},
                revenueLabel: "{{ __('Revenue') }} ($)",
                paymentLabels: ["{{ __('Cash') }}", "{{ __('Card') }}", "{{ __('QR Scan') }}"],
                paymentData: [{{ $cashCount }}, {{ $cardCount }}, {{ $qrCount }}],
                chartTextColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#d1d5db' : '#4b5563',
                chartGridColor: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)'
            };
        </script>
        <script defer src="{{ asset('js/pages/dashboard-index.js') }}"></script>
    @endpush
@endsection
