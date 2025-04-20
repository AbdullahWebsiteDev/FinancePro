<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get all budget records
$budgets = getAllBudgets();

// Page title
$pageTitle = "Budget Management";
?>

<?php include 'includes/header.php'; ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Budget Management</h1>
    <p class="text-gray-600">Add and manage your budget allocations</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Add Budget Form -->
    <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-1">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Budget</h2>
        <form id="addBudgetForm" class="space-y-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" name="date" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="name" name="name" required placeholder="Budget name or description"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" id="amount" name="amount" required placeholder="0.00"
                        class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Budget
                </button>
            </div>
        </form>
    </div>
    
    <!-- Budget List -->
    <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Budget Records</h2>
        
        <?php if (count($budgets) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="budgetTableBody">
                        <?php foreach ($budgets as $budget): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($budget['date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($budget['name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                $<?php echo number_format($budget['amount'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No budget records found</p>
                <p class="text-gray-400 text-sm mt-1">Add your first budget using the form</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Success Alert (Hidden by default) -->
<div id="successAlert" class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Success!</p>
    <p>Budget has been added successfully.</p>
</div>

<!-- Error Alert (Hidden by default) -->
<div id="errorAlert" class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Error!</p>
    <p id="errorMessage">Something went wrong. Please try again.</p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/budget.js"></script>
