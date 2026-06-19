/**
 * Dashboard Modern - Chart.js Integration & Interactive Features
 * Handles revenue charts, payment analytics, animations, and responsive behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initializeSalesChart();
    initializePaymentChart();

    // Initialize animations
    initializeCounterAnimations();

    // Initialize responsive behavior
    handleResponsive();
});

/**
 * Initialize Sales Revenue Chart
 */
function initializeSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const config = window.dashboardConfig;

    const chartCanvas = ctx.getContext('2d');

    // Create gradient
    const gradient = chartCanvas.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

    new Chart(chartCanvas, {
        type: 'line',
        data: {
            labels: config.chartDates,
            datasets: [{
                label: config.revenueLabel,
                data: config.chartSales,
                borderColor: '#0d6efd',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#0d6efd',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return '$ ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    display: true,
                    grid: {
                        color: config.chartGridColor,
                        drawBorder: false,
                        drawTicks: false
                    },
                    ticks: {
                        color: config.chartTextColor,
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return '$ ' + formatNumber(value);
                        }
                    }
                },
                x: {
                    display: true,
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: config.chartTextColor,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize Payment Methods Chart
 */
function initializePaymentChart() {
    const ctx = document.getElementById('paymentChart');
    if (!ctx) return;

    const config = window.dashboardConfig;
    const colors = ['#28a745', '#0d6efd', '#6f42c1'];
    const borderColors = ['#fff', '#fff', '#fff'];

    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: config.paymentLabels,
            datasets: [{
                data: config.paymentData,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 3,
                borderRadius: 8,
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            interaction: {
                intersect: false
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed * 100) / total);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize Counter Animations
 * Animates numbers from 0 to their final value
 */
function initializeCounterAnimations() {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseFloat(counter.innerText.replace(/[$,]/g, ''));
        const isUSD = counter.innerText.includes('$');
        const duration = 2000; // 2 seconds
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += increment;

            if (step >= steps) {
                current = target;
                clearInterval(timer);
            }

            if (isUSD) {
                counter.innerText = '$' + formatNumber(current.toFixed(2));
            } else {
                counter.innerText = formatNumber(Math.floor(current));
            }
        }, duration / steps);
    });
}

/**
 * Format number with thousand separators
 */
function formatNumber(num) {
    if (typeof num === 'string') {
        num = parseFloat(num);
    }
    return num.toLocaleString('en-US', {
        minimumFractionDigits: num % 1 !== 0 ? 2 : 0,
        maximumFractionDigits: 2
    });
}

/**
 * Handle responsive behavior
 */
function handleResponsive() {
    const resize = () => {
        // Add any responsive behavior here
    };

    window.addEventListener('resize', resize);
}

/**
 * Utility: Toggle Element Visibility
 */
function toggleElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.classList.toggle('d-none');
    }
}

/**
 * Utility: Copy to Clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(() => {
        showToast('Failed to copy', 'error');
    });
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed bottom-0 end-0 m-3`;
    toast.innerHTML = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

/**
 * Utility: Format Date
 */
function formatDate(date) {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Auto-refresh dashboard data (optional)
 * Uncomment to enable auto-refresh every 5 minutes
 */
// setTimeout(() => {
//     location.reload();
// }, 5 * 60 * 1000);

/**
 * Export Dashboard Data (utility function)
 */
function exportDashboardData() {
    const data = {
        exportDate: new Date().toISOString(),
        pageTitle: document.title,
        charts: window.dashboardConfig
    };

    const dataStr = JSON.stringify(data, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'dashboard-export.json';
    link.click();
}

/**
 * Print Dashboard
 */
function printDashboard() {
    window.print();
}

// Export functions for global use
window.exportDashboardData = exportDashboardData;
window.printDashboard = printDashboard;
window.copyToClipboard = copyToClipboard;
window.showToast = showToast;
