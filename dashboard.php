<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get summary data
$totalBalance = getTotalBalance();
$totalExpense = getTotalExpense();
$totalPettyCash = getTotalPettyCash();
$todayExpense = getTodayExpense();

// Get recent transactions for the dashboard
$recentExpenses = getRecentExpenses(5);

// Page title
$pageTitle = "Dashboard";
?>

<?php include 'includes/header.php'; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Total Balance -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Total Balance</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">$<?php echo number_format($totalBalance, 2); ?></h3>
            </div>
            <div class="rounded-full bg-blue-100 dark:bg-blue-900 p-3">
                <i class="fas fa-wallet text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Expense -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-red-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Total Expense</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">$<?php echo number_format($totalExpense, 2); ?></h3>
            </div>
            <div class="rounded-full bg-red-100 dark:bg-red-900 p-3">
                <i class="fas fa-shopping-cart text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Petty Cash -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-green-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Total Petty Cash</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">$<?php echo number_format($totalPettyCash, 2); ?></h3>
            </div>
            <div class="rounded-full bg-green-100 dark:bg-green-900 p-3">
                <i class="fas fa-cash-register text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Today's Expense -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-purple-500">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Today's Expense</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">$<?php echo number_format($todayExpense, 2); ?></h3>
            </div>
            <div class="rounded-full bg-purple-100 dark:bg-purple-900 p-3">
                <i class="fas fa-calendar-day text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Chart -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Financial Overview</h3>
        <div class="h-80">
            <canvas id="financeChart"></canvas>
        </div>
    </div>
    
    <!-- Recent activity -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Activity</h3>
        <div class="space-y-4">
            <div class="bg-gray-50 dark:bg-gray-600 p-4 rounded-lg">
                <h4 class="font-medium text-gray-700 dark:text-gray-200 mb-2">Recent Expenses</h4>
                <?php if (count($recentExpenses) > 0): ?>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-500">
                        <?php foreach ($recentExpenses as $expense): ?>
                        <li class="py-2 flex justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($expense['item']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M d, Y', strtotime($expense['date'])); ?></p>
                            </div>
                            <p class="text-sm font-semibold text-red-500 dark:text-red-400">-$<?php echo number_format($expense['cost'], 2); ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-600 dark:text-gray-300">No recent expenses found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="expense.php" class="flex items-center justify-center bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 p-4 rounded-lg hover:bg-red-200 dark:hover:bg-red-700 transition duration-200 w-full sm:w-auto">
                <i class="fas fa-plus-circle mr-2"></i>
                <span>Add Expense</span>
            </a>
            <a href="petty-cash.php" class="flex items-center justify-center bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-lg hover:bg-green-200 dark:hover:bg-green-700 transition duration-200 w-full sm:w-auto">
                <i class="fas fa-plus-circle mr-2"></i>
                <span>Add Petty Cash</span>
            </a>
            <a href="reports.php" class="flex items-center justify-center bg-purple-100 dark:bg-purple-800 text-purple-700 dark:text-purple-200 p-4 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-700 transition duration-200 w-full sm:w-auto">
                <i class="fas fa-chart-bar mr-2"></i>
                <span>View Reports</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/chart-config.js"></script>
<script src="assets/js/dashboard.js"></script>
