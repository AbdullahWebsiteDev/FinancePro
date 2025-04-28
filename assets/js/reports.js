document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const exportExpenseBtn = document.getElementById('exportExpenseBtn');
    const exportPettyCashBtn = document.getElementById('exportPettyCashBtn');

    // Initialize date inputs with current month
    if (!startDateInput.value) {
        startDateInput.value = new Date().toISOString().slice(0, 8) + '01';
    }
    if (!endDateInput.value) {
        const lastDay = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0);
        endDateInput.value = lastDay.toISOString().slice(0, 10);
    }

    // Add event listeners for date changes
    startDateInput.addEventListener('change', updateData);
    endDateInput.addEventListener('change', updateData);

    // Add event listeners for export buttons
    if (exportExpenseBtn) {
        exportExpenseBtn.addEventListener('click', () => generatePDFReport('expense'));
    }
    if (exportPettyCashBtn) {
        exportPettyCashBtn.addEventListener('click', () => generatePDFReport('petty-cash'));
    }
});

function updateData() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        showErrorAlert('Please select both start and end dates.');
        return;
    }

    if (new Date(endDate) < new Date(startDate)) {
        showErrorAlert('End date must be after start date.');
        return;
    }

    // Reload page with new date parameters
    window.location.href = `reports.php?start_date=${startDate}&end_date=${endDate}`;
}

function generatePDFReport(reportType) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        showErrorAlert('Please select both start and end dates.');
        return;
    }

    if (new Date(endDate) < new Date(startDate)) {
        showErrorAlert('End date must be after start date.');
        return;
    }

    // Show loading state
    const button = reportType === 'expense' ? 
        document.getElementById('exportExpenseBtn') : 
        document.getElementById('exportPettyCashBtn');
    const originalText = button.textContent;
    button.textContent = 'Generating...';
    button.disabled = true;

    // First get the preview
    fetch(`api/reports.php?action=getPreview&type=${reportType}&start_date=${startDate}&end_date=${endDate}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                // Show preview section
                const previewSection = document.getElementById('reportPreview');
                const reportContent = document.getElementById('reportContent');
                
                // Update preview content
                reportContent.innerHTML = data.html;
                previewSection.classList.remove('hidden');
                
                // Scroll to preview
                previewSection.scrollIntoView({ behavior: 'smooth' });
                
                // Setup print button
                document.getElementById('printReportBtn').onclick = () => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                            <head>
                                <title>Financial Report (${reportType.charAt(0).toUpperCase() + reportType.slice(1)})</title>
                                <style>
                                    body { 
                                        font-family: Arial, sans-serif;
                                        padding: 20px;
                                    }
                                    table { 
                                        width: 100%;
                                        border-collapse: collapse;
                                        margin-top: 20px;
                                    }
                                    th, td { 
                                        padding: 8px;
                                        border: 1px solid #ddd;
                                        text-align: left;
                                    }
                                    th { 
                                        background-color: #f5f5f5;
                                    }
                                    h1 {
                                        color: #333;
                                        margin-bottom: 20px;
                                    }
                                    .total {
                                        font-weight: bold;
                                    }
                                    @media print {
                                        body { 
                                            padding: 0;
                                        }
                                        button {
                                            display: none;
                                        }
                                    }
                                </style>
                            </head>
                            <body>
                                <h1>${reportType.charAt(0).toUpperCase() + reportType.slice(1)} Report</h1>
                                <p>Period: ${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()}</p>
                                ${data.html}
                                <script>
                                    window.onload = () => {
                                        window.print();
                                        setTimeout(() => window.close(), 500);
                                    };
                                </script>
                            </body>
                        </html>
                    `);
                    printWindow.document.close();
                };

                showSuccessAlert('Report preview generated successfully');
            } else {
                throw new Error(data.message || 'Failed to generate preview');
            }
        })
        .catch(error => {
            console.error('Error generating preview:', error);
            showErrorAlert(error.message || 'Error generating preview. Please try again.');
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
}

function showSuccessAlert(message) {
    const alert = document.getElementById('successAlert');
    const messageElement = document.getElementById('successMessage');
    if (alert && messageElement) {
        messageElement.textContent = message;
        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 3000);
    }
}

function showErrorAlert(message) {
    const alert = document.getElementById('errorAlert');
    const messageElement = document.getElementById('errorMessage');
    if (alert && messageElement) {
        messageElement.textContent = message;
        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
    }
}
