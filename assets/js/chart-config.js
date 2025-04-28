/**
 * Chart.js global configuration options
 */

// Define common chart colors
const chartColors = {
    blue: '#3B82F6',
    red: '#EF4444',
    green: '#10B981',
    purple: '#8B5CF6',
    orange: '#F59E0B',
    teal: '#14B8A6',
    indigo: '#6366F1',
    yellow: '#FBBF24',
    pink: '#EC4899'
};

// Define common chart backgrounds with transparency
const chartBackgrounds = {
    blue: 'rgba(59, 130, 246, 0.1)',
    red: 'rgba(239, 68, 68, 0.1)',
    green: 'rgba(16, 185, 129, 0.1)',
    purple: 'rgba(139, 92, 246, 0.1)',
    orange: 'rgba(245, 158, 11, 0.1)',
    teal: 'rgba(20, 184, 166, 0.1)',
    indigo: 'rgba(99, 102, 241, 0.1)',
    yellow: 'rgba(251, 191, 36, 0.1)',
    pink: 'rgba(236, 72, 153, 0.1)'
};

// Common font for all charts
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"';
Chart.defaults.font.size = 12;

// Common colors and styles
Chart.defaults.color = '#6B7280';
Chart.defaults.borderColor = '#E5E7EB';

// Common padding
Chart.defaults.layout.padding = 16;

// Disable the chart.js legend click handler that hides datasets
Chart.defaults.plugins.legend.onClick = function(e, legendItem, legend) {
    const index = legendItem.datasetIndex;
    const meta = legend.chart.getDatasetMeta(index);
    
    // Toggle visibility with reduced opacity instead of hiding
    meta.hidden = meta.hidden === true ? false : true;
    
    // Update the chart
    legend.chart.update();
};

// Custom tooltip formatter for currency values
Chart.Tooltip.positioners.cursor = function(chartElements, coordinates) {
    return coordinates;
};

// Common currency formatter for tooltips
const currencyFormatter = new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 2
});

// Common options for line charts
const commonLineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index',
        intersect: false,
    },
    plugins: {
        legend: {
            position: 'top',
            labels: {
                usePointStyle: true,
                boxWidth: 6,
                padding: 20
            }
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#1F2937',
            bodyColor: '#4B5563',
            borderColor: '#E5E7EB',
            borderWidth: 1,
            cornerRadius: 4,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += currencyFormatter.format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                padding: 10
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                padding: 10,
                callback: function(value) {
                    return '₨' + value.toLocaleString();
                }
            }
        }
    },
    elements: {
        line: {
            tension: 0.3
        },
        point: {
            radius: 3,
            hoverRadius: 5
        }
    }
};

// Common options for doughnut/pie charts
const commonDoughnutChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: {
                usePointStyle: true,
                padding: 20
            }
        },
        tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#1F2937',
            bodyColor: '#4B5563',
            borderColor: '#E5E7EB',
            borderWidth: 1,
            cornerRadius: 4,
            padding: 12,
            callbacks: {
                label: function(context) {
                    let label = context.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed !== null) {
                        label += currencyFormatter.format(context.parsed);
                    }
                    return label;
                }
            }
        }
    },
    cutout: '70%',
    borderWidth: 0
};

// Common options for bar charts
const commonBarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 20
            }
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#1F2937',
            bodyColor: '#4B5563',
            borderColor: '#E5E7EB',
            borderWidth: 1,
            cornerRadius: 4,
            padding: 12,
            callbacks: {
                label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += currencyFormatter.format(context.parsed.y);
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                padding: 10
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                padding: 10,
                callback: function(value) {
                    return '₨' + value.toLocaleString();
                }
            }
        }
    },
    borderRadius: 4,
    barPercentage: 0.7,
    categoryPercentage: 0.7
};
