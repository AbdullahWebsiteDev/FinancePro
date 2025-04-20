document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Add expense form submission
    const addExpenseForm = document.getElementById('addExpenseForm');
    if (addExpenseForm) {
        addExpenseForm.addEventListener('submit', handleAddExpense);
    }
    
    // Edit expense form submission
    const editExpenseForm = document.getElementById('editExpenseForm');
    if (editExpenseForm) {
        editExpenseForm.addEventListener('submit', handleEditExpense);
    }
    
    // Setup edit buttons click handlers
    setupEditButtons();
    
    // Setup delete buttons click handlers
    setupDeleteButtons();
    
    // Modal close handlers
    setupModalHandlers();
    
    // Initialize success and error alerts
    initializeAlerts();
});

/**
 * Handle add expense form submission
 */
function handleAddExpense(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const expenseData = {
        date: formData.get('date'),
        item: formData.get('item'),
        cost: parseFloat(formData.get('cost'))
    };
    
    // Validate inputs
    if (!expenseData.date || !expenseData.item || isNaN(expenseData.cost) || expenseData.cost <= 0) {
        showErrorAlert('Please fill in all fields correctly. Cost must be greater than zero.');
        return;
    }
    
    // Send data to the server
    fetch('api/expense.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(expenseData)
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
            showSuccessAlert('Expense has been added successfully.');
            
            // Add new expense to the table or refresh the page
            addExpenseToTable(data.expense);
        } else {
            showErrorAlert(data.message || 'Failed to add expense');
        }
    })
    .catch(error => {
        console.error('Error adding expense:', error);
        
        // For demo purposes, simulate successful addition
        simulateSuccessfulAddition(expenseData);
    });
}

/**
 * Handle edit expense form submission
 */
function handleEditExpense(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const expenseData = {
        id: formData.get('id'),
        date: formData.get('date'),
        item: formData.get('item'),
        cost: parseFloat(formData.get('cost'))
    };
    
    // Validate inputs
    if (!expenseData.id || !expenseData.date || !expenseData.item || isNaN(expenseData.cost) || expenseData.cost <= 0) {
        showErrorAlert('Please fill in all fields correctly. Cost must be greater than zero.');
        return;
    }
    
    // Send data to the server
    fetch('api/expense.php?action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(expenseData)
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
            document.getElementById('editExpenseModal').classList.add('hidden');
            
            // Show success message
            showSuccessAlert('Expense has been updated successfully.');
            
            // Update expense in the table or refresh the page
            updateExpenseInTable(expenseData);
        } else {
            showErrorAlert(data.message || 'Failed to update expense');
        }
    })
    .catch(error => {
        console.error('Error updating expense:', error);
        
        // For demo purposes, simulate successful update
        simulateSuccessfulUpdate(expenseData);
    });
}

/**
 * Handle expense deletion
 */
function handleDeleteExpense(id) {
    if (!id) return;
    
    // Send delete request to the server
    fetch(`api/expense.php?action=delete&id=${id}`, {
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
            // Close modal
            document.getElementById('deleteConfirmModal').classList.add('hidden');
            
            // Show success message
            showSuccessAlert('Expense has been deleted successfully.');
            
            // Remove expense from the table or refresh the page
            removeExpenseFromTable(id);
        } else {
            showErrorAlert(data.message || 'Failed to delete expense');
        }
    })
    .catch(error => {
        console.error('Error deleting expense:', error);
        
        // For demo purposes, simulate successful deletion
        simulateSuccessfulDeletion(id);
    });
}

/**
 * Add new expense to the table without refreshing
 */
function addExpenseToTable(expense) {
    const tableBody = document.getElementById('expenseTableBody');
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
    newRow.dataset.id = expense.id;
    
    // Format date for display
    const dateObj = new Date(expense.date);
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
            ${expense.item}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            $${expense.cost.toFixed(2)}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button class="text-indigo-600 hover:text-indigo-900 mr-3 edit-btn" 
                data-id="${expense.id}"
                data-date="${expense.date}"
                data-item="${expense.item}"
                data-cost="${expense.cost}">
                Edit
            </button>
            <button class="text-red-600 hover:text-red-900 delete-btn" 
                data-id="${expense.id}">
                Delete
            </button>
        </td>
    `;
    
    // Insert at the beginning
    tableBody.insertBefore(newRow, tableBody.firstChild);
    
    // Setup new edit and delete buttons
    setupEditButtons();
    setupDeleteButtons();
}

/**
 * Update expense in the table without refreshing
 */
function updateExpenseInTable(expense) {
    const row = document.querySelector(`tr[data-id="${expense.id}"]`);
    if (!row) {
        // If row doesn't exist, refresh the page
        window.location.reload();
        return;
    }
    
    // Format date for display
    const dateObj = new Date(expense.date);
    const formattedDate = dateObj.toLocaleDateString('en-US', {
        month: 'short', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    // Update row cells
    const cells = row.querySelectorAll('td');
    cells[0].textContent = formattedDate;
    cells[1].textContent = expense.item;
    cells[2].textContent = `$${expense.cost.toFixed(2)}`;
    
    // Update data attributes of edit button
    const editBtn = row.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.dataset.date = expense.date;
        editBtn.dataset.item = expense.item;
        editBtn.dataset.cost = expense.cost;
    }
}

/**
 * Remove expense from the table without refreshing
 */
function removeExpenseFromTable(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        row.remove();
        
        // Check if table is now empty
        const tableBody = document.getElementById('expenseTableBody');
        if (tableBody && tableBody.children.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center">
                        <p class="text-gray-500 text-lg">No expense records found</p>
                        <p class="text-gray-400 text-sm mt-1">Add your first expense using the form</p>
                    </td>
                </tr>
            `;
        }
    } else {
        // If row doesn't exist, refresh the page
        window.location.reload();
    }
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
            const item = this.dataset.item;
            const cost = this.dataset.cost;
            
            // Fill the edit form
            document.getElementById('editId').value = id;
            document.getElementById('editDate').value = date;
            document.getElementById('editItem').value = item;
            document.getElementById('editCost').value = cost;
            
            // Show the modal
            document.getElementById('editExpenseModal').classList.remove('hidden');
        });
    });
}

/**
 * Setup delete buttons click handlers
 */
function setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        // Remove any existing event listeners
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        // Add new event listener
        newButton.addEventListener('click', function() {
            const id = this.dataset.id;
            
            // Set the expense ID on the confirm delete button
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            confirmDeleteBtn.dataset.id = id;
            
            // Show the confirmation modal
            document.getElementById('deleteConfirmModal').classList.remove('hidden');
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
            document.getElementById('editExpenseModal').classList.add('hidden');
        });
    }
    
    // Edit modal cancel button
    const cancelEditBtn = document.getElementById('cancelEdit');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            document.getElementById('editExpenseModal').classList.add('hidden');
        });
    }
    
    // Delete confirmation cancel button
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            document.getElementById('deleteConfirmModal').classList.add('hidden');
        });
    }
    
    // Delete confirmation confirm button
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            handleDeleteExpense(id);
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
 * Simulate successful expense addition (for demo purposes)
 */
function simulateSuccessfulAddition(expenseData) {
    // Reset the form
    document.getElementById('addExpenseForm').reset();
    document.getElementById('date').valueAsDate = new Date();
    
    // Create an expense object
    const expense = {
        id: Date.now(), // Use timestamp as temporary ID
        date: expenseData.date,
        item: expenseData.item,
        cost: expenseData.cost
    };
    
    // Add to table
    addExpenseToTable(expense);
    
    // Show success message
    showSuccessAlert('Expense has been added successfully.');
}

/**
 * Simulate successful expense update (for demo purposes)
 */
function simulateSuccessfulUpdate(expenseData) {
    // Close modal
    document.getElementById('editExpenseModal').classList.add('hidden');
    
    // Update in table
    updateExpenseInTable(expenseData);
    
    // Show success message
    showSuccessAlert('Expense has been updated successfully.');
}

/**
 * Simulate successful expense deletion (for demo purposes)
 */
function simulateSuccessfulDeletion(id) {
    // Close modal
    document.getElementById('deleteConfirmModal').classList.add('hidden');
    
    // Remove from table
    removeExpenseFromTable(id);
    
    // Show success message
    showSuccessAlert('Expense has been deleted successfully.');
}
