@extends('layouts.app')

@section('title', __('Dashboard Overview'))


@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                    <div>
                        {{-- <h5 class="mb-1 fw-bold text-primary">{{ __('Dashboard Range') }}</h5> --}}
                        <p class="text-muted mb-0">{{ $rangeLabel }}</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle rounded-pill px-4 py-2" type="button" id="dashboardRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $periodOptions[$selectedPeriod] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardRangeDropdown">
                            @foreach ($periodOptions as $value => $label)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center {{ $selectedPeriod === $value ? 'active' : '' }}" href="{{ route('dashboard', ['period' => $value]) }}">
                                        <span>{{ $label }}</span>
                                        @if ($selectedPeriod === $value)
                                            <i class="fas fa-check text-primary"></i>
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

    {{-- <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start  shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Revenue') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-primary">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-primary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Total Debt') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-warning">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-warning opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('General Expenses') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalExpenses, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('Product_Purchases') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalPurchases, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Estimated Profit') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-success">${{ number_format($netProfit, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Total Orders') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-info">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-info opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                {{ __('Total Products') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-secondary">{{ number_format($totalProducts) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box-open fa-2x text-secondary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                {{ __('Total Categories') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-dark">{{ number_format($totalCategories) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-tags fa-2x text-dark opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row g-2 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start  shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Revenue') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-primary">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-primary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Total Debt') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-warning">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-warning opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('General Expenses') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalExpenses, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('Product_Purchases') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalPurchases, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Estimated Profit') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-success">${{ number_format($netProfit, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-start shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Total Orders') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-info">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-shopping-cart fa-2x text-info opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow h-100 bg-white">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Top Selling Products') }}</h6>
                </div>
                <div class="card-body">
                    @if ($topSellingProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th class="small text-secondary">#</th>
                                        <th class="small text-secondary">{{ __('Product') }}</th>
                                        <th class="small text-secondary text-end">{{ __('Sold') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topSellingProducts as $index => $item)
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            <td>{{ optional($item->product)->Name ?? __('Unknown Product') }}</td>
                                            <td class="text-end">{{ number_format($item->quantity_sold) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            {{ __('No sales data available yet.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow h-100 bg-white">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Recent Transactions') }}</h6>
                    <span class="small text-muted">{{ __('Last 5 orders') }}</span>
                </div>
                <div class="card-body">
                    @if ($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->OrderDate ? \Carbon\Carbon::parse($transaction->OrderDate)->format('d M Y H:i') : '' }}
                                            </td>
                                            <td>{{ optional($transaction->customer)->Name ?? __('Walk-in') }}</td>
                                            <td>${{ number_format($transaction->TotalAmount, 2) }}</td>
                                            <td>{{ __($transaction->Status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            {{ __('No recent transactions yet.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow h-100 bg-white">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Revenue Graph') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow h-100 bg-white">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Payment Methods') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="card shadow mb-4 border-start">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-danger">{{ __('Low/Out of Stock Items') }}</h6>
            <span class="badge bg-warning text-dark">{{ $lowStockItems->count() }} {{ __('items') }}</span>
        </div>
        <div class="card-body">
            @if ($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Stock Left') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockItems as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->Name }}</td>
                                    <td class="fw-bold">
                                        <span class="text-danger fs-5">{{ $item->inventory->Quantity ?? 0 }}</span>
                                        @if (($item->inventory->Quantity ?? 0) > 0)
                                            <span class="text-warning small ms-2"><i
                                                    class="fas fa-exclamation-triangle"></i> {{ __('Low Stock') }}</span>
                                        @else
                                            <span class="text-danger small ms-2"><i class="fas fa-times-circle"></i>
                                                {{ __('Out of Stock') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.index') }}"
                                            class="btn btn-sm btn-outline-primary fw-bold">
                                            <i class="fas fa-plus"></i> {{ __('Restock Now') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                    <p class="text-muted mb-0">{{ __('No low stock items. All stock levels are safe!') }}</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            window.dashboardIndexConfig = {
                chartDates: {!! json_encode($chartDates) !!},
                chartSales: {!! json_encode($chartSales) !!},
                revenueLabel: "{{ __('Revenue') }} ($)",
                paymentLabels: ["{{ __('Cash') }}", "{{ __('Card') }}", "{{ __('QR Scan') }}"],
                paymentData: [{{ $cashCount }}, {{ $cardCount }}, {{ $qrCount }}],
                chartTextColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#d1d5db' : '#6b7280',
                chartGridColor: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
            };
        </script>
        <script src="{{ asset('js/pages/dashboard-index.js') }}"></script>
    @endpush
@endsection
