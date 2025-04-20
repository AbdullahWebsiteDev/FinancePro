document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Add petty cash form submission
    const addPettyCashForm = document.getElementById('addPettyCashForm');
    if (addPettyCashForm) {
        addPettyCashForm.addEventListener('submit', handleAddPettyCash);
    }
    
    // Initialize success and error alerts
    initializeAlerts();
});

/**
 * Handle add petty cash form submission
 */
function handleAddPettyCash(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const pettyCashData = {
        date: formData.get('date'),
        title: formData.get('title'),
        amount: parseFloat(formData.get('amount'))
    };
    
    // Validate inputs
    if (!pettyCashData.date || !pettyCashData.title || isNaN(pettyCashData.amount) || pettyCashData.amount <= 0) {
        showErrorAlert('Please fill in all fields correctly. Amount must be greater than zero.');
        return;
    }
    
    // Send data to the server
    fetch('api/petty-cash.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(pettyCashData)
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
            
            // Add new petty cash to the table or refresh the page
            addPettyCashToTable(data.pettyCash);
        } else {
            showErrorAlert(data.message || 'Failed to add petty cash');
        }
    })
    .catch(error => {
        console.error('Error adding petty cash:', error);
        
        // For demo purposes, simulate successful addition
        simulateSuccessfulAddition(pettyCashData);
    });
}

/**
 * Add new petty cash to the table without refreshing
 */
function addPettyCashToTable(pettyCash) {
    const tableBody = document.getElementById('pettyCashTableBody');
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
    const dateObj = new Date(pettyCash.date);
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
            ${pettyCash.title}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            $${pettyCash.amount.toFixed(2)}
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
 * Simulate successful petty cash addition (for demo purposes)
 */
function simulateSuccessfulAddition(pettyCashData) {
    // Reset the form
    document.getElementById('addPettyCashForm').reset();
    document.getElementById('date').valueAsDate = new Date();
    
    // Create a petty cash object
    const pettyCash = {
        id: Date.now(), // Use timestamp as temporary ID
        date: pettyCashData.date,
        title: pettyCashData.title,
        amount: pettyCashData.amount
    };
    
    // Add to table
    addPettyCashToTable(pettyCash);
    
    // Show success message
    showSuccessAlert();
}
