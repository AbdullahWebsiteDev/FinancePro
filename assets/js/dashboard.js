document.addEventListener('DOMContentLoaded', function() {
    // Get the financial data for the chart
    fetchFinancialData();
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
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Budget',
                    data: data.budget,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Expense',
                    data: data.expense,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Petty Cash',
                    data: data.pettyCash,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
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
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
