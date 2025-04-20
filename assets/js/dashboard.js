document.addEventListener('DOMContentLoaded', function() {
    // Get the financial data for the chart
    fetchFinancialData();
    
    // Setup dark mode chart updates
    setupDarkModeChartUpdates();
});

/**
 * Fetch financial data for dashboard charts
 */
function fetchFinancialData() {
    fetch('api/reports.php?action=getMonthlyData')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Create chart with the data
            createFinancialChart(data);
        })
        .catch(error => {
            console.error('Error fetching financial data:', error);
            
            // If API fails, create chart with sample data for demonstration
            const sampleData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                budget: [4500, 5000, 4800, 5200, 5500, 6000],
                expense: [3800, 4200, 4500, 4300, 4800, 5100],
                pettyCash: [300, 350, 320, 280, 350, 400]
            };
            createFinancialChart(sampleData);
        });
}

/**
 * Create financial overview chart
 */
function createFinancialChart(data) {
    const ctx = document.getElementById('financeChart').getContext('2d');
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Set colors based on mode
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
    const textColor = isDarkMode ? '#e5e7eb' : '#6B7280';
    
    // Create and store chart instance globally so it can be updated when toggling dark mode
    window.financeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Budget',
                    data: data.budget,
                    borderColor: '#3B82F6',
                    backgroundColor: isDarkMode ? 'rgba(59, 130, 246, 0.2)' : 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Expense',
                    data: data.expense,
                    borderColor: '#EF4444',
                    backgroundColor: isDarkMode ? 'rgba(239, 68, 68, 0.2)' : 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Petty Cash',
                    data: data.pettyCash,
                    borderColor: '#10B981',
                    backgroundColor: isDarkMode ? 'rgba(16, 185, 129, 0.2)' : 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: textColor
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: isDarkMode ? 'rgba(55, 65, 81, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                    titleColor: isDarkMode ? '#e5e7eb' : '#1F2937',
                    bodyColor: isDarkMode ? '#d1d5db' : '#4B5563',
                    borderColor: isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US', {
                                    style: 'currency',
                                    currency: 'USD'
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        color: gridColor
                    },
                    ticks: {
                        color: textColor
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

/**
 * Setup dark mode chart updates
 */
function setupDarkModeChartUpdates() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            // If chart exists, update it when dark mode changes
            if (window.financeChart) {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                const textColor = isDarkMode ? '#e5e7eb' : '#6B7280';
                
                // Update dataset colors
                window.financeChart.data.datasets.forEach((dataset, i) => {
                    if (i === 0) { // Budget
                        dataset.backgroundColor = isDarkMode ? 'rgba(59, 130, 246, 0.2)' : 'rgba(59, 130, 246, 0.1)';
                    } else if (i === 1) { // Expense
                        dataset.backgroundColor = isDarkMode ? 'rgba(239, 68, 68, 0.2)' : 'rgba(239, 68, 68, 0.1)';
                    } else if (i === 2) { // Petty Cash
                        dataset.backgroundColor = isDarkMode ? 'rgba(16, 185, 129, 0.2)' : 'rgba(16, 185, 129, 0.1)';
                    }
                });
                
                // Update tooltip colors
                window.financeChart.options.plugins.tooltip.backgroundColor = isDarkMode ? 'rgba(55, 65, 81, 0.9)' : 'rgba(255, 255, 255, 0.9)';
                window.financeChart.options.plugins.tooltip.titleColor = isDarkMode ? '#e5e7eb' : '#1F2937';
                window.financeChart.options.plugins.tooltip.bodyColor = isDarkMode ? '#d1d5db' : '#4B5563';
                window.financeChart.options.plugins.tooltip.borderColor = isDarkMode ? 'rgba(75, 85, 99, 0.3)' : 'rgba(229, 231, 235, 1)';
                
                // Update legend colors
                window.financeChart.options.plugins.legend.labels.color = textColor;
                
                // Update scales colors
                window.financeChart.options.scales.x.grid.color = gridColor;
                window.financeChart.options.scales.y.grid.color = gridColor;
                window.financeChart.options.scales.x.ticks.color = textColor;
                window.financeChart.options.scales.y.ticks.color = textColor;
                
                // Update the chart
                window.financeChart.update();
            }
        });
    }
}
