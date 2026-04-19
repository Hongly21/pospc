@extends('layouts.app')

@section('title', __('Dashboard Overview'))


@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-primary shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Total Revenue') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-primary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-warning shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Total Debt') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-warning">${{ number_format($totalDebt, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-warning opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-danger shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('General Expenses') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalExpenses, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-success shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Estimated Profit') }}</div>
                            <div class="h4 mb-0 font-weight-bold text-success">${{ number_format($netProfit, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Weekly Revenue Graph') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
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


    <div class="card shadow mb-4 border-start border-4 border-warning">
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
                                            <span class="text-warning small ms-2"><i class="fas fa-exclamation-triangle"></i> {{ __('Low Stock') }}</span>
                                        @else
                                            <span class="text-danger small ms-2"><i class="fas fa-times-circle"></i> {{ __('Out of Stock') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary fw-bold">
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const chartTextColor = isDark ? '#d1d5db' : '#6b7280';
            const chartGridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

            const ctx1 = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartDates) !!},
                    datasets: [{
                        label: "{{ __('Revenue') }} ($)",
                        data: {!! json_encode($chartSales) !!},
                        backgroundColor: '#4e73df',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: chartTextColor,
                                font: { family: 'Kantumruy Pro', size: 12 }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: chartTextColor },
                            grid: { color: chartGridColor }
                        },
                        y: {
                            ticks: { color: chartTextColor },
                            grid: { color: chartGridColor }
                        }
                    }
                }
            });

            const ctx2 = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ["{{ __('Cash') }}", "{{ __('Card') }}", "{{ __('QR Scan') }}"],
                    datasets: [{
                        data: [{{ $cashCount }}, {{ $cardCount }}, {{ $qrCount }}],
                        backgroundColor: ['#1cc88a', '#4e73df', '#36b9cc'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: chartTextColor,
                                font: { family: 'Kantumruy Pro', size: 12 }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
