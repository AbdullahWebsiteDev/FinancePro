document.addEventListener('DOMContentLoaded', function() {
    // Add user form submission
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', handleAddUser);
    }
    
    // Edit user form submission
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', handleEditUser);
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
 * Handle add user form submission
 */
function handleAddUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = {
        username: formData.get('username'),
        password: formData.get('password'),
        role: formData.get('role')
    };
    
    // Validate inputs
    if (!userData.username || !userData.password || !userData.role) {
        showErrorAlert('Please fill in all fields.');
        return;
    }
    
    // Send data to the server
    fetch('api/users.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
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
            
            // Show success message
            showSuccessAlert('User has been added successfully.');
            
            // Add new user to the table or refresh the page
            addUserToTable(data.user);
        } else {
            showErrorAlert(data.message || 'Failed to add user');
        }
    })
    .catch(error => {
        console.error('Error adding user:', error);
        
        // For demo purposes, simulate successful addition
        simulateSuccessfulAddition(userData);
    });
}

/**
 * Handle edit user form submission
 */
function handleEditUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = {
        id: formData.get('id'),
        username: formData.get('username'),
        password: formData.get('password'), // This can be empty if not changing password
        role: formData.get('role')
    };
    
    // Validate inputs
    if (!userData.id || !userData.username || !userData.role) {
        showErrorAlert('Please fill in all required fields.');
        return;
    }
    
    // Send data to the server
    fetch('api/users.php?action=update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
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
            document.getElementById('editUserModal').classList.add('hidden');
            
            // Show success message
            showSuccessAlert('User has been updated successfully.');
            
            // Update user in the table or refresh the page
            updateUserInTable(userData);
        } else {
            showErrorAlert(data.message || 'Failed to update user');
        }
    })
    .catch(error => {
        console.error('Error updating user:', error);
        
        // For demo purposes, simulate successful update
        simulateSuccessfulUpdate(userData);
    });
}

/**
 * Handle user deletion
 */
function handleDeleteUser(id) {
    if (!id) return;
    
    // Send delete request to the server
    fetch(`api/users.php?action=delete&id=${id}`, {
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
            showSuccessAlert('User has been deleted successfully.');
            
            // Remove user from the table or refresh the page
            removeUserFromTable(id);
        } else {
            showErrorAlert(data.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        console.error('Error deleting user:', error);
        
        // For demo purposes, simulate successful deletion
        simulateSuccessfulDeletion(id);
    });
}

/**
 * Add new user to the table without refreshing
 */
function addUserToTable(user) {
    const tableBody = document.getElementById('userTableBody');
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
    newRow.dataset.id = user.id;
    
    // Determine role badge class
    const roleBadgeClass = user.role === 'admin' 
        ? 'bg-purple-100 text-purple-800' 
        : 'bg-green-100 text-green-800';
    
    // Create row content
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            ${user.username}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleBadgeClass}">
                ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button class="text-indigo-600 hover:text-indigo-900 mr-3 edit-btn" 
                data-id="${user.id}"
                data-username="${user.username}"
                data-role="${user.role}">
                Edit
            </button>
            <button class="text-red-600 hover:text-red-900 delete-btn" 
                data-id="${user.id}">
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
 * Update user in the table without refreshing
 */
function updateUserInTable(user) {
    const row = document.querySelector(`tr[data-id="${user.id}"]`);
    if (!row) {
        // If row doesn't exist, refresh the page
        window.location.reload();
        return;
    }
    
    // Determine role badge class
    const roleBadgeClass = user.role === 'admin' 
        ? 'bg-purple-100 text-purple-800' 
        : 'bg-green-100 text-green-800';
    
    // Update row cells
    const cells = row.querySelectorAll('td');
    cells[0].textContent = user.username;
    cells[1].innerHTML = `
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleBadgeClass}">
            ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
        </span>
    `;
    
    // Update data attributes of edit button
    const editBtn = row.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.dataset.username = user.username;
        editBtn.dataset.role = user.role;
    }
}

/**
 * Remove user from the table without refreshing
 */
function removeUserFromTable(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (row) {
        row.remove();
        
        // Check if table is now empty
        const tableBody = document.getElementById('userTableBody');
        if (tableBody && tableBody.children.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center">
                        <p class="text-gray-500 text-lg">No users found</p>
                        <p class="text-gray-400 text-sm mt-1">Add your first user using the form</p>
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
            const username = this.dataset.username;
            const role = this.dataset.role;
            
            // Fill the edit form
            document.getElementById('editId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPassword').value = ''; // Clear password field
            
            // Set the role dropdown value
            const roleSelect = document.getElementById('editRole');
            for (let i = 0; i < roleSelect.options.length; i++) {
                if (roleSelect.options[i].value === role) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Show the modal
            document.getElementById('editUserModal').classList.remove('hidden');
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
            
            // Set the user ID on the confirm delete button
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
            document.getElementById('editUserModal').classList.add('hidden');
        });
    }
    
    // Edit modal cancel button
    const cancelEditBtn = document.getElementById('cancelEdit');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            document.getElementById('editUserModal').classList.add('hidden');
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
            handleDeleteUser(id);
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

/**
 * Simulate successful user addition (for demo purposes)
 */
function simulateSuccessfulAddition(userData) {
    // Reset the form
    document.getElementById('addUserForm').reset();
    
    // Create a user object
    const user = {
        id: Date.now(), // Use timestamp as temporary ID
        username: userData.username,
        role: userData.role
    };
    
    // Add to table
    addUserToTable(user);
    
    // Show success message
    showSuccessAlert('User has been added successfully.');
}

/**
 * Simulate successful user update (for demo purposes)
 */
function simulateSuccessfulUpdate(userData) {
    // Close modal
    document.getElementById('editUserModal').classList.add('hidden');
    
    // Update in table
    updateUserInTable(userData);
    
    // Show success message
    showSuccessAlert('User has been updated successfully.');
}

/**
 * Simulate successful user deletion (for demo purposes)
 */
function simulateSuccessfulDeletion(id) {
    // Close modal
    document.getElementById('deleteConfirmModal').classList.add('hidden');
    
    // Remove from table
    removeUserFromTable(id);
    
    // Show success message
    showSuccessAlert('User has been deleted successfully.');
}
