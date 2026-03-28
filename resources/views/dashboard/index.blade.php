@extends('layouts.app')

@section('title', 'ទិដ្ឋភាពទូទៅនៃផ្ទាំងគ្រប់គ្រងការលក់')

@section('content')
    <style>
        @media (max-width: 768px) {
            .chart-box {
                height: 220px !important;
            }
        }

        body {
            font-family: 'Kantumruy Pro', sans-serif;
        }
    </style>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-primary shadow h-100 py-2 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ចំណូលលក់សរុប (Revenue)</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-primary opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-warning shadow h-100 py-2 bg-light">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ប្រាក់កំពុងជំពាក់ (Total Debt)</div>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">ចំណាយទូទៅ (Expenses)</div>
                            <div class="h4 mb-0 font-weight-bold text-danger">-${{ number_format($totalExpenses, 2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-success shadow h-100 py-2" style="background-color: #f8fff9;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ប្រាក់ចំណេញប៉ាន់ស្មាន (Profit)</div>
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
                    <h6 class="m-0 fw-bold text-primary">ក្រាហ្វប្រាក់ចំណូលប្រចាំសប្តាហ៍</h6>
                </div>
                <div class="card-body">
                    <div class="chart-box" style="height: 300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">វិធីសាស្រ្តទូទាត់ប្រាក់</h6>
                </div>
                <div class="card-body">
                    <div class="chart-box" style="height: 300px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-start border-4 border-warning">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-danger">ទំនិញដែលជិតអស់ពីស្តុក និងអស់ពីស្តុក (Low/Out of Stock)</h6>
            <span class="badge bg-warning text-dark">{{ $lowStockItems->count() }} មុខ</span>
        </div>
        <div class="card-body">
            @if ($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>ឈ្មោះទំនិញ (Product Name)</th>
                                <th>ស្តុកនៅសល់ (Stock Left)</th>
                                <th class="text-center">សកម្មភាព (Action)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockItems as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->Name }}</td>
                                    <td class="fw-bold">
                                        <span class="text-danger fs-5">{{ $item->inventory->Quantity ?? 0 }}</span>

                                        @if (($item->inventory->Quantity ?? 0) > 0)
                                            <span class="text-warning small ms-2"><i class="fas fa-exclamation-triangle"></i> ជិតអស់ស្តុក</span>
                                        @else
                                            <span class="text-danger small ms-2"><i class="fas fa-times-circle"></i> អស់ពីស្តុក</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary fw-bold">
                                            <i class="fas fa-plus"></i> Restock Now
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
                    <p class="text-muted mb-0">មិនមានទំនិញជិតអស់ពីស្តុកឡើយ ស្តុកទាំងអស់មានសុវត្ថិភាពល្អ!</p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Sales Chart
            const ctx1 = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartDates) !!},
                    datasets: [{
                        label: 'ប្រាក់ចំណូល ($)',
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
                                font: { family: 'Khmer OS Battambang', size: 12 }
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { font: { family: 'Khmer OS Battambang' } } },
                        y: { ticks: { font: { family: 'Khmer OS Battambang' } } }
                    }
                }
            });

            // 2. Payment Chart
            const ctx2 = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['ប្រាក់សុទ្ធ', 'កាត', 'ស្កេន QR'],
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
                            labels: { font: { family: 'Khmer OS Battambang', size: 12 } }
                        }
                    }
                }
            });
        });
    </script>
@endsection
