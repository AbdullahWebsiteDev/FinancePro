<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Get all users
$users = getAllUsers();

// Page title
$pageTitle = "User Management";
?>

<?php include 'includes/header.php'; ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">User Management</h1>
    <p class="text-gray-600">Add and manage system users</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Add User Form -->
    <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-1">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New User</h2>
        <form id="addUserForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter username"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            
            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add User
                </button>
            </div>
        </form>
    </div>
    
    <!-- User List -->
    <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">User List</h2>
        
        <?php if (count($users) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr data-id="<?php echo $user['id']; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900 mr-3 edit-btn" 
                                    data-id="<?php echo $user['id']; ?>"
                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                    data-role="<?php echo $user['role']; ?>">
                                    Edit
                                </button>
                                <?php if ($_SESSION['user_id'] != $user['id']): ?>
                                <button class="text-red-600 hover:text-red-900 delete-btn" 
                                    data-id="<?php echo $user['id']; ?>">
                                    Delete
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No users found</p>
                <p class="text-gray-400 text-sm mt-1">Add your first user using the form</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit User Modal (Hidden by default) -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editUserForm" class="space-y-4">
            <input type="hidden" id="editId" name="id">
            
            <div>
                <label for="editUsername" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="editUsername" name="username" required placeholder="Enter username"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="editPassword" name="password" placeholder="Enter new password (leave blank to keep current)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
            </div>
            
            <div>
                <label for="editRole" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="editRole" name="role" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            
            <div class="flex space-x-4">
                <button type="button" id="cancelEdit"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal (Hidden by default) -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
            <p class="text-gray-600 mt-2">Are you sure you want to delete this user? This action cannot be undone.</p>
        </div>
        
        <div class="flex space-x-4">
            <button id="cancelDelete"
                class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </button>
            <button id="confirmDelete"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Success Alert (Hidden by default) -->
<div id="successAlert" class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Success!</p>
    <p id="successMessage">Operation completed successfully.</p>
</div>

<!-- Error Alert (Hidden by default) -->
<div id="errorAlert" class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Error!</p>
    <p id="errorMessage">Something went wrong. Please try again.</p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/users.js"></script>
