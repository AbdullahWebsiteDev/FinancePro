<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function getAllBudgets() {
    global $db; // Assuming $db is your database connection
    $query = "SELECT * FROM budgets";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all budget records
$budgets = getAllBudgets();

// Page title
$pageTitle = "Budget Management";
?>

<?php include 'includes/header.php'; ?>

<div class="mb-6">
    <h1 class="mb-2 text-2xl font-bold text-gray-800">Budget Management</h1>
    <p class="text-gray-600">Add and manage your budget allocations</p>
</div>

<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
    <!-- Add Budget Form -->
    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-1">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Add New Budget</h2>
        <form id="addBudgetForm" class="space-y-4">
            <div>
                <label for="date" class="block mb-1 text-sm font-medium text-gray-700">Date</label>
                <input type="date" id="date" name="date" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" required placeholder="Budget name or description"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label for="amount" class="block mb-1 text-sm font-medium text-gray-700">Amount</label>
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₨</span>
                    </div>
                    <input type="number" step="0.01" min="0" id="amount" name="amount" required placeholder="0.00"
                        class="w-full py-2 pr-3 border border-gray-300 rounded-md shadow-sm pl-7 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <button type="submit" 
                    class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Add Budget
                </button>
            </div>
        </form>
    </div>
    
    <!-- Budget List -->
    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-2">
        <h2 class="mb-4 text-lg font-semibold text-gray-800">Budget Records</h2>
        
        <?php if (count($budgets) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="budgetTableBody">
                        <?php foreach ($budgets as $budget): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <?php echo date('M d, Y', strtotime($budget['date'])); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                <?php echo htmlspecialchars($budget['name']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                ₨<?php echo number_format($budget['amount'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="py-8 text-center">
                <p class="text-lg text-gray-500">No budget records found</p>
                <p class="mt-1 text-sm text-gray-400">Add your first budget using the form</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Success Alert (Hidden by default) -->
<div id="successAlert" class="fixed hidden p-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded shadow-md bottom-4 right-4" role="alert">
    <p class="font-bold">Success!</p>
    <p>Budget has been added successfully.</p>
</div>

<!-- Error Alert (Hidden by default) -->
<div id="errorAlert" class="fixed hidden p-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded shadow-md bottom-4 right-4" role="alert">
    <p class="font-bold">Error!</p>
    <p id="errorMessage">Something went wrong. Please try again.</p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/budget.js"></script>
