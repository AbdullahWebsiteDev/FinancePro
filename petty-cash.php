<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all petty cash records
$pettyCash = getAllPettyCash();

// Page title
$pageTitle = "Petty Cash Management";
?>

<?php include 'includes/header.php'; ?>

<div class="mb-6">
    <h1 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Petty Cash Management</h1>
    <p class="text-gray-600 dark:text-gray-300">Add and manage your petty cash records</p>
</div>

<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
    <!-- Add Petty Cash Form -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700 lg:col-span-1">
        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Add New Petty Cash</h2>
        <form id="addPettyCashForm" class="space-y-4">
            <div>
                <label for="date" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Date</label>
                <input type="date" id="date" name="date" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="title" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Title</label>
                <input type="text" id="title" name="title" required placeholder="Petty cash title or description"
                    class="w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="amount" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Amount</label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">₨</span>
                    </div>
                    <input type="number" step="0.01" min="0" id="amount" name="amount" required placeholder="0.00"
                        class="w-full py-2 pr-3 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm pl-7 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Petty Cash
                </button>
            </div>
        </form>
    </div>

    <!-- Petty Cash List -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700 lg:col-span-2">
        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Petty Cash Records</h2>

        <?php if (count($pettyCash) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Date</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Title</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Amount</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600" id="pettyCashTableBody">
                        <?php foreach ($pettyCash as $record): ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                    <?php echo date('M d, Y', strtotime($record['date'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?php echo htmlspecialchars($record['title']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                    ₨<?php echo number_format($record['amount'], 2); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                    <button class="mr-3 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 edit-btn" 
                                        data-id="<?php echo $record['id']; ?>"
                                        data-date="<?php echo $record['date']; ?>"
                                        data-title="<?php echo htmlspecialchars($record['title']); ?>"
                                        data-amount="<?php echo $record['amount']; ?>">
                                        Edit
                                    </button>
                                    <button onclick="deletePettyCash(<?php echo $record['id']; ?>)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="py-8 text-center">
                <p class="text-lg text-gray-500 dark:text-gray-300">No petty cash records found</p>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-400">Add your first petty cash using the form</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Petty Cash Modal (Hidden by default) -->
<div id="editPettyCashModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-50">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Petty Cash</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editPettyCashForm" class="space-y-4">
            <input type="hidden" id="editId" name="id">
            
            <div>
                <label for="editDate" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Date</label>
                <input type="date" id="editDate" name="date" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="editTitle" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Title</label>
                <input type="text" id="editTitle" name="title" required placeholder="Petty cash description"
                    class="w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="editAmount" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Amount</label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">₨</span>
                    </div>
                    <input type="number" step="0.01" min="0" id="editAmount" name="amount" required placeholder="0.00"
                        class="w-full py-2 pr-3 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm pl-7 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button type="button" id="cancelEdit"
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:text-gray-200 dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button type="submit" 
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Petty Cash
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Alert (Hidden by default) -->
<div id="successAlert" class="fixed hidden p-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded shadow-md bottom-4 right-4 dark:bg-green-900 dark:text-green-200" role="alert">
    <p class="font-bold">Success!</p>
    <p id="successMessage">Operation completed successfully.</p>
</div>

<!-- Error Alert (Hidden by default) -->
<div id="errorAlert" class="fixed hidden p-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded shadow-md bottom-4 right-4 dark:bg-red-900 dark:text-red-200" role="alert">
    <p class="font-bold">Error!</p>
    <p id="errorMessage">Something went wrong. Please try again.</p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/petty-cash.js"></script>
<script>
    function deletePettyCash(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            fetch(`api/petty-cash.php?id=${id}`, {
                method: 'DELETE',
            })
            .then(response => {
                if (response.ok) {
                    alert('Record deleted successfully.');
                    location.reload();
                } else {
                    alert('Failed to delete the record.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the record.');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = document.getElementById('editPettyCashModal');
        const closeModal = document.getElementById('closeModal');
        const cancelEdit = document.getElementById('cancelEdit');
        const editForm = document.getElementById('editPettyCashForm');

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const date = button.getAttribute('data-date');
                const title = button.getAttribute('data-title');
                const amount = button.getAttribute('data-amount');

                document.getElementById('editId').value = id;
                document.getElementById('editDate').value = date;
                document.getElementById('editTitle').value = title;
                document.getElementById('editAmount').value = amount;

                editModal.classList.remove('hidden');
            });
        });

        closeModal.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        cancelEdit.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });

        editForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const formData = new FormData(editForm);
            const jsonData = Object.fromEntries(formData.entries());

            fetch('api/petty-cash.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(jsonData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessage').textContent = data.message;
                    document.getElementById('successAlert').classList.remove('hidden');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    document.getElementById('errorMessage').textContent = data.message;
                    document.getElementById('errorAlert').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errorMessage').textContent = 'An error occurred while updating the record.';
                document.getElementById('errorAlert').classList.remove('hidden');
            });
        });
    });
</script>