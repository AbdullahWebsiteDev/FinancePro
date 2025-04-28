document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Add petty cash form submission
    const addPettyCashForm = document.getElementById('addPettyCashForm');
    if (addPettyCashForm) {
        addPettyCashForm.addEventListener('submit', handleAddPettyCash);
    }
    
    // Edit petty cash form submission
    const editPettyCashForm = document.getElementById('editPettyCashForm');
    if (editPettyCashForm) {
        editPettyCashForm.addEventListener('submit', handleEditPettyCash);
    }
    
    // Setup edit buttons click handlers
    setupEditButtons();
    
    // Modal close handlers
    setupModalHandlers();
    
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
            showSuccessAlert('Petty cash has been added successfully');
            
            // Add new petty cash to the table or refresh the page
            addPettyCashToTable(data.pettyCash);
        } else {
            showErrorAlert(data.message || 'Failed to add petty cash');
        }
    })
    .catch(error => {
        console.error('Error adding petty cash:', error);
        showErrorAlert('An error occurred while adding petty cash');
    });
}

/**
 * Handle edit petty cash form submission
 */
function handleEditPettyCash(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const pettyCashData = {
        id: formData.get('id'),
        date: formData.get('date'),
        title: formData.get('title'),
        amount: parseFloat(formData.get('amount'))
    };
    
    // Validate inputs
    if (!pettyCashData.id || !pettyCashData.date || !pettyCashData.title || isNaN(pettyCashData.amount) || pettyCashData.amount <= 0) {
        showErrorAlert('Please fill in all fields correctly. Amount must be greater than zero.');
        return;
    }
    
    // Send data to the server
    fetch('api/petty-cash.php?action=update', {
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
            // Close modal
            document.getElementById('editPettyCashModal').classList.add('hidden');
            
            // Show success message
            showSuccessAlert('Petty cash has been updated successfully');
            
            // Update data in table
            updatePettyCashInTable(pettyCashData);
        } else {
            showErrorAlert(data.message || 'Failed to update petty cash');
        }
    })
    .catch(error => {
        console.error('Error updating petty cash:', error);
        showErrorAlert('An error occurred while updating petty cash');
    });
}

/**
 * Update petty cash in the table without refreshing
 */
function updatePettyCashInTable(pettyCash) {
    const row = document.querySelector(`tr[data-id="${pettyCash.id}"]`);
    if (!row) {
        // If row doesn't exist, refresh the page
        window.location.reload();
        return;
    }
    
    // Format date for display
    const dateObj = new Date(pettyCash.date);
    const formattedDate = dateObj.toLocaleDateString('en-US', {
        month: 'short', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    // Update row cells
    const cells = row.querySelectorAll('td');
    cells[0].textContent = formattedDate;
    cells[1].textContent = pettyCash.title;
    cells[2].textContent = `₨${pettyCash.amount.toFixed(2)}`;
    
    // Update data attributes of edit button
    const editBtn = row.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.dataset.date = pettyCash.date;
        editBtn.dataset.title = pettyCash.title;
        editBtn.dataset.amount = pettyCash.amount;
    }
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
    newRow.dataset.id = pettyCash.id;
    
    // Format date for display
    const dateObj = new Date(pettyCash.date);
    const formattedDate = dateObj.toLocaleDateString('en-US', {
        month: 'short', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
            ${formattedDate}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
            ${pettyCash.title}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
            ₨${pettyCash.amount.toFixed(2)}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3 edit-btn"
                data-id="${pettyCash.id}"
                data-date="${pettyCash.date}"
                data-title="${pettyCash.title}"
                data-amount="${pettyCash.amount}">
                Edit
            </button>
            <button onclick="deletePettyCash(${pettyCash.id})"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                Delete
            </button>
        </td>
    `;
    
    // Insert at the beginning
    tableBody.insertBefore(newRow, tableBody.firstChild);
    
    // Setup new edit buttons
    setupEditButtons();
}

/**
 * Setup edit buttons click handlers
 */
function setupEditButtons() {
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        // Remove any existing event listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        // Add new event listener
        newButton.addEventListener('click', function() {
            const id = this.dataset.id;
            const date = this.dataset.date;
            const title = this.dataset.title;
            const amount = this.dataset.amount;
            
            // Fill the edit form
            document.getElementById('editId').value = id;
            document.getElementById('editDate').value = date;
            document.getElementById('editTitle').value = title;
            document.getElementById('editAmount').value = amount;
            
            // Show the modal
            document.getElementById('editPettyCashModal').classList.remove('hidden');
        });
    });
}

/**
 * Setup modal handlers
 */
function setupModalHandlers() {
    // Edit modal close button
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            document.getElementById('editPettyCashModal').classList.add('hidden');
        });
    }
    
    // Edit modal cancel button
    const cancelEditBtn = document.getElementById('cancelEdit');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            document.getElementById('editPettyCashModal').classList.add('hidden');
        });
    }
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
    
    if (successAlert) {
        if (successMessage && message) {
            successMessage.textContent = message;
        }
        
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
 * Delete petty cash record
 */
function deletePettyCash(id) {
    if (confirm('Are you sure you want to delete this petty cash record?')) {
        fetch(`api/petty-cash.php?action=delete&id=${id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    row.remove();
                } else {
                    window.location.reload();
                }
                showSuccessAlert('Petty cash deleted successfully');
            } else {
                showErrorAlert(data.message || 'Failed to delete petty cash');
            }
        })
        .catch(error => {
            console.error('Error deleting petty cash:', error);
            showErrorAlert('Error deleting petty cash. Please try again.');
        });
    }
}
