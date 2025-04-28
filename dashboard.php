<?php
// Everything before session_start and any output must be kept together
// to avoid "headers already sent" errors
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Get summary data
    $totalBalance = getTotalBalance();
    $totalExpense = getTotalExpense();
    $totalPettyCash = getTotalPettyCash();
    $todayExpense = getTodayExpense();
    
    // Get recent transactions for the dashboard
    $recentExpenses = getRecentExpenses(5);
} catch (Exception $e) {
    // Log the error
    error_log("Error in dashboard: " . $e->getMessage());
    
    // Set default values
    $totalBalance = 0;
    $totalExpense = 0;
    $totalPettyCash = 0;
    $todayExpense = 0;
    $recentExpenses = [];
}

// Page title
$pageTitle = "Dashboard";
?>

<?php include 'includes/header.php'; ?>

<div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2 lg:grid-cols-4">
    <!-- Total Balance -->
    <div class="p-6 bg-blue-200 border-l-4 border-blue-500 rounded-lg shadow-md dark:bg-blue-950">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Balance</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">₨<?php echo number_format($totalBalance, 2); ?></h3>
            </div>
            <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-900">
                <i class="text-xl text-blue-500 fas fa-wallet"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Expense -->
    <div class="p-6 bg-red-200 border-l-4 border-red-500 rounded-lg shadow-md dark:bg-red-950">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Expense</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">₨<?php echo number_format($totalExpense, 2); ?></h3>
            </div>
            <div class="p-3 bg-red-100 rounded-full dark:bg-red-900">
                <i class="text-xl text-red-500 fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Petty Cash -->
    <div class="p-6 bg-green-200 border-l-4 border-green-500 rounded-lg shadow-md dark:bg-green-950">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Petty Cash</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">₨<?php echo number_format($totalPettyCash, 2); ?></h3>
            </div>
            <div class="p-3 bg-green-100 rounded-full dark:bg-green-900">
                <i class="text-xl text-green-500 fas fa-cash-register"></i>
            </div>
        </div>
    </div>
    
    <!-- Today's Expense -->
    <div class="p-6 bg-purple-200 border-l-4 border-purple-500 rounded-lg shadow-md dark:bg-purple-950">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Today's Expense</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">₨<?php echo number_format($todayExpense, 2); ?></h3>
            </div>
            <div class="p-3 bg-purple-100 rounded-full dark:bg-purple-900">
                <i class="text-xl text-purple-500 fas fa-calendar-day"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
    <!-- Chart -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700">
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Financial Overview</h3>
        <div class="h-80">
            <canvas id="financeChart"></canvas>
        </div>
    </div>
    
    <!-- Recent activity -->
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700">
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Recent Activity</h3>
        <div class="space-y-4">
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-600">
                <h4 class="mb-2 font-medium text-gray-700 dark:text-gray-200">Recent Expenses</h4>
                <?php if (count($recentExpenses) > 0): ?>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-500">
                        <?php foreach ($recentExpenses as $expense): ?>
                        <li class="flex justify-between py-2">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($expense['item']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo date('M d, Y', strtotime($expense['date'])); ?></p>
                            </div>
                            <p class="text-sm font-semibold text-red-500 dark:text-red-400">-₨<?php echo number_format($expense['cost'], 2); ?></p>
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
    <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700">
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="expense.php" class="flex items-center justify-center w-full p-4 text-red-700 transition duration-200 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-700 sm:w-auto">
                <i class="mr-2 fas fa-plus-circle"></i>
                <span>Add Expense</span>
            </a>
            <a href="petty-cash.php" class="flex items-center justify-center w-full p-4 text-green-700 transition duration-200 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-700 sm:w-auto">
                <i class="mr-2 fas fa-plus-circle"></i>
                <span>Add Petty Cash</span>
            </a>
            <a href="reports.php" class="flex items-center justify-center w-full p-4 text-purple-700 transition duration-200 bg-purple-100 rounded-lg dark:bg-purple-800 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-700 sm:w-auto">
                <i class="mr-2 fas fa-chart-bar"></i>
                <span>View Reports</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/chart-config.js"></script>
<script src="assets/js/dashboard.js"></script>
