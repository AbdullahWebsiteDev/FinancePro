<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Default date range (current month)
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');

// If date range is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? date('Y-m-01');
    $endDate = $_POST['end_date'] ?? date('Y-m-t');
}

// Get filtered data
$expenses = getExpensesByDateRange($startDate, $endDate);
$budgets = getBudgetsByDateRange($startDate, $endDate);
$pettyCash = getPettyCashByDateRange($startDate, $endDate);

// Calculate totals
$totalExpense = 0;
foreach ($expenses as $expense) {
    $totalExpense += $expense['cost'];
}

$totalBudget = 0;
foreach ($budgets as $budget) {
    $totalBudget += $budget['amount'];
}

$totalPettyCash = 0;
foreach ($pettyCash as $record) {
    $totalPettyCash += $record['amount'];
}

// Page title
$pageTitle = "Financial Reports";
?>

<?php include 'includes/header.php'; ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Financial Reports</h1>
    <p class="text-gray-600">Generate and export financial reports</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Reports</h2>
    <form method="POST" action="reports.php" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-end">
        <div class="md:flex-1">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div class="md:flex-1">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        
        <div class="md:flex-none">
            <button type="submit" 
                class="w-full md:w-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Apply Filter
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Summary Cards -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Budget Summary</h3>
        <p class="text-3xl font-bold text-gray-900 mb-2">$<?php echo number_format($totalBudget, 2); ?></p>
        <p class="text-sm text-gray-500">Total Budget for selected period</p>
        <button id="exportBudgetBtn" class="mt-4 flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-file-pdf mr-1"></i>
            <span>Export as PDF</span>
        </button>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Expense Summary</h3>
        <p class="text-3xl font-bold text-gray-900 mb-2">$<?php echo number_format($totalExpense, 2); ?></p>
        <p class="text-sm text-gray-500">Total Expenses for selected period</p>
        <button id="exportExpenseBtn" class="mt-4 flex items-center text-red-600 hover:text-red-800">
            <i class="fas fa-file-pdf mr-1"></i>
            <span>Export as PDF</span>
        </button>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Petty Cash Summary</h3>
        <p class="text-3xl font-bold text-gray-900 mb-2">$<?php echo number_format($totalPettyCash, 2); ?></p>
        <p class="text-sm text-gray-500">Total Petty Cash for selected period</p>
        <button id="exportPettyCashBtn" class="mt-4 flex items-center text-green-600 hover:text-green-800">
            <i class="fas fa-file-pdf mr-1"></i>
            <span>Export as PDF</span>
        </button>
    </div>
</div>

<!-- Detailed Reports Tabs -->
<div class="bg-white rounded-lg shadow-md mb-8">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex" aria-label="Tabs">
            <button id="expenseTab" class="tab-button active border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                Expenses
            </button>
            <button id="budgetTab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                Budgets
            </button>
            <button id="pettyCashTab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                Petty Cash
            </button>
        </nav>
    </div>
    
    <!-- Expense Report Tab -->
    <div id="expenseTabContent" class="tab-content p-6 block">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense Report</h3>
        <?php if (count($expenses) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($expense['date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($expense['item']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                $<?php echo number_format($expense['cost'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                Total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                $<?php echo number_format($totalExpense, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No expense records found for this date range</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Budget Report Tab -->
    <div id="budgetTabContent" class="tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Budget Report</h3>
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
                    <tbody class="bg-white divide-y divide-gray-200">
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
                        <tr class="bg-gray-50">
                            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                Total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                $<?php echo number_format($totalBudget, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No budget records found for this date range</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Petty Cash Report Tab -->
    <div id="pettyCashTabContent" class="tab-content p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Petty Cash Report</h3>
        <?php if (count($pettyCash) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($pettyCash as $record): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($record['date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($record['title']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                $<?php echo number_format($record['amount'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="bg-gray-50">
                            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                Total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                $<?php echo number_format($totalPettyCash, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No petty cash records found for this date range</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Success Alert (Hidden by default) -->
<div id="successAlert" class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Success!</p>
    <p id="successMessage">PDF report has been generated and downloaded.</p>
</div>

<!-- Error Alert (Hidden by default) -->
<div id="errorAlert" class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md hidden" role="alert">
    <p class="font-bold">Error!</p>
    <p id="errorMessage">Something went wrong generating the PDF. Please try again.</p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/reports.js"></script>
