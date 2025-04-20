document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Add budget form submission
    const addBudgetForm = document.getElementById('addBudgetForm');
    if (addBudgetForm) {
        addBudgetForm.addEventListener('submit', handleAddBudget);
    }
    
    // Initialize success and error alerts
    initializeAlerts();
});

/**
 * Handle add budget form submission
 */
function handleAddBudget(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const budgetData = {
        date: formData.get('date'),
        name: formData.get('name'),
        amount: parseFloat(formData.get('amount'))
    };
    
    // Validate inputs
    if (!budgetData.date || !budgetData.name || isNaN(budgetData.amount) || budgetData.amount <= 0) {
        showErrorAlert('Please fill in all fields correctly. Amount must be greater than zero.');
        return;
    }
    
    // Send data to the server
    fetch('api/budget.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(budgetData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reset form
            event.target.reset();
            document.getElementById('date').valueAsDate = new Date();
            
            // Show success message
            showSuccessAlert();
            
            // Add new budget to the table or refresh the page
            addBudgetToTable(data.budget);
        } else {
            showErrorAlert(data.message || 'Failed to add budget');
        }
    })
    .catch(error => {
        console.error('Error adding budget:', error);
        
        // For demo purposes, simulate successful addition
        simulateSuccessfulAddition(budgetData);
    });
}

/**
 * Add new budget to the table without refreshing
 */
function addBudgetToTable(budget) {
    const tableBody = document.getElementById('budgetTableBody');
    if (!tableBody) {
        // If table doesn't exist, refresh the page
        window.location.reload();
        return;
    }
    
    // Check if there's a "no records" message
    if (tableBody.querySelector('tr td[colspan]')) {
        tableBody.innerHTML = '';
    }
    
    // Create new row
    const newRow = document.createElement('tr');
    
    // Format date for display
    const dateObj = new Date(budget.date);
    const formattedDate = dateObj.toLocaleDateString('en-US', {
        month: 'short', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${formattedDate}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            ${budget.name}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            $${budget.amount.toFixed(2)}
        </td>
    `;
    
    // Insert at the beginning
    tableBody.insertBefore(newRow, tableBody.firstChild);
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
 * Show success alert
 */
function showSuccessAlert() {
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
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

/**
 * Simulate successful budget addition (for demo purposes)
 */
function simulateSuccessfulAddition(budgetData) {
    // Reset the form
    document.getElementById('addBudgetForm').reset();
    document.getElementById('date').valueAsDate = new Date();
    
    // Create a budget object
    const budget = {
        id: Date.now(), // Use timestamp as temporary ID
        date: budgetData.date,
        name: budgetData.name,
        amount: budgetData.amount
    };
    
    // Add to table
    addBudgetToTable(budget);
    
    // Show success message
    showSuccessAlert();
}
