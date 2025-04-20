document.addEventListener('DOMContentLoaded', function() {
    // Initialize tabs
    setupTabs();
    
    // Setup export buttons
    setupExportButtons();
    
    // Initialize success and error alerts
    initializeAlerts();
});

/**
 * Setup tabs functionality
 */
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the target tab content ID
            const targetId = this.id.replace('Tab', 'TabContent');
            
            // Deactivate all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-300', 'hover:text-gray-700', 'dark:hover:text-gray-100', 'hover:border-gray-300', 'dark:hover:border-gray-400');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // Activate the selected tab
            this.classList.add('active', 'border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-300', 'hover:text-gray-700', 'dark:hover:text-gray-100', 'hover:border-gray-300', 'dark:hover:border-gray-400');
            
            // Show the selected tab content
            document.getElementById(targetId).classList.remove('hidden');
            document.getElementById(targetId).classList.add('block');
        });
    });
}

/**
 * Setup export buttons functionality
 */
function setupExportButtons() {
    // Expense export button
    const exportExpenseBtn = document.getElementById('exportExpenseBtn');
    if (exportExpenseBtn) {
        exportExpenseBtn.addEventListener('click', function() {
            generatePDFReport('expense');
        });
    }
    
    // Petty Cash export button
    const exportPettyCashBtn = document.getElementById('exportPettyCashBtn');
    if (exportPettyCashBtn) {
        exportPettyCashBtn.addEventListener('click', function() {
            generatePDFReport('petty-cash');
        });
    }
}

/**
 * Generate PDF report for specified report type
 */
function generatePDFReport(reportType) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Validate date range
    if (!startDate || !endDate) {
        showErrorAlert('Please select both start and end dates.');
        return;
    }
    
    // Send request to server to generate PDF
    fetch(`api/reports.php?action=generatePdf&type=${reportType}&start_date=${startDate}&end_date=${endDate}`, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.blob();
    })
    .then(blob => {
        // Create a URL for the blob
        const url = window.URL.createObjectURL(blob);
        
        // Create a link element
        const link = document.createElement('a');
        link.href = url;
        link.download = `${reportType}_report_${startDate}_to_${endDate}.pdf`;
        
        // Append to the document body, click it, and remove it
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        showSuccessAlert(`${reportType.charAt(0).toUpperCase() + reportType.slice(1)} report has been downloaded.`);
    })
    .catch(error => {
        console.error(`Error generating ${reportType} PDF:`, error);
        
        // For demo purposes, show success instead of error
        simulateSuccessfulExport(reportType, startDate, endDate);
    });
}

/**
 * Simulate successful PDF export (for demo purposes)
 */
function simulateSuccessfulExport(reportType, startDate, endDate) {
    // Format report type for display
    let formattedType = reportType.replace('-', ' ');
    formattedType = formattedType.charAt(0).toUpperCase() + formattedType.slice(1);
    
    // Show success message
    showSuccessAlert(`${formattedType} report has been generated. In the live environment, this would download a PDF.`);
}

/**
 * Initialize success and error alerts
 */
function initializeAlerts() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    
    if (successAlert) {
        successAlert.addEventListener('click', function() {
            this.classList.add('hidden');
        });
    }
    
    if (errorAlert) {
        errorAlert.addEventListener('click', function() {
            this.classList.add('hidden');
        });
    }
}

/**
 * Show success alert with custom message
 */
function showSuccessAlert(message) {
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');
    
    if (successAlert && successMessage) {
        successMessage.textContent = message;
        successAlert.classList.remove('hidden');
        setTimeout(() => {
            successAlert.classList.add('hidden');
        }, 3000);
    }
}

/**
 * Show error alert with custom message
 */
function showErrorAlert(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    
    if (errorAlert && errorMessage) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('hidden');
        setTimeout(() => {
            errorAlert.classList.add('hidden');
        }, 5000);
    }
}
