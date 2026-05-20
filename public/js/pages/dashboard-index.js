document.addEventListener('DOMContentLoaded', function() {
    const config = window.dashboardIndexConfig || {};
    const chartTextColor = config.chartTextColor || '#6b7280';
    const chartGridColor = config.chartGridColor || 'rgba(0,0,0,0.1)';

    const ctx1 = document.getElementById('salesChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: config.chartDates || [],
                datasets: [{
                    label: config.revenueLabel || 'Revenue ($)',
                    data: config.chartSales || [],
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
                            font: {
                                family: 'Kantumruy Pro',
                                size: 12
                            }
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
    }

    const ctx2 = document.getElementById('paymentChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: config.paymentLabels || ['Cash', 'Card', 'QR Scan'],
                datasets: [{
                    data: config.paymentData || [],
                    backgroundColor: ['#1cc88a', '#4e73df', '#36b9cc']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: chartTextColor,
                            font: {
                                family: 'Kantumruy Pro',
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
});
