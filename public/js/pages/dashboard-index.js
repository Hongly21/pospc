document.addEventListener('DOMContentLoaded', function () {
    const config = window.dashboardIndexConfig || {};
    const chartTextColor = config.chartTextColor || '#4b5563';
    const chartGridColor = config.chartGridColor || 'rgba(0,0,0,0.05)';

    // 1. Revenue Bar Chart Setup
    const ctx1 = document.getElementById('salesChart');
    if (ctx1) {
        const barCtx = ctx1.getContext('2d');
        // Elegant vibrant blue gradient fill instead of stark flat solid color
        const revenueGradient = barCtx.createLinearGradient(0, 0, 0, 300);
        revenueGradient.addColorStop(0, '#3B82F6');
        revenueGradient.addColorStop(1, '#1D4ED8');

        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: config.chartDates || [],
                datasets: [{
                    label: config.revenueLabel || 'Revenue ($)',
                    data: config.chartSales || [],
                    backgroundColor: revenueGradient,
                    hoverBackgroundColor: '#1E40AF',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Cleaner interface without basic redundancy labels
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 8,
                        backgroundColor: '#1f2937'
                    }
                },
                scales: {
                    x: {
                        ticks: { color: chartTextColor, font: { family: 'Kantumruy Pro', size: 11 } },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: chartTextColor, font: { family: 'Kantumruy Pro', size: 11 } },
                        grid: { color: chartGridColor, drawTicks: false }
                    }
                }
            }
        });
    }

    // 2. Payment Doughnut Chart Setup
    const ctx2 = document.getElementById('paymentChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: config.paymentLabels || ['Cash', 'Card', 'QR Scan'],
                datasets: [{
                    data: config.paymentData || [],
                    backgroundColor: [
                        '#10B981', // Emerald Cash
                        '#3B82F6', // Blue Card
                        '#8B5CF6'  // Purple KHQR
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: chartTextColor,
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: 'Kantumruy Pro', size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                cutout: '75%' // Thinner, modern look doughnut ring profile
            }
        });
    }
});
