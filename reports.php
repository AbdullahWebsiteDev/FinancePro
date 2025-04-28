<?php
session_start();
require_once 'includes/database.php';

// Get totals for each section
$totalExpense = getTotalExpense();
$totalPettyCash = getTotalPettyCash();

// Get date range
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

$pageTitle = "Financial Reports";
?>

<?php include 'includes/header.php'; ?>

<div class="container px-4 py-8 mx-auto">
    <div class="mb-6">
        <h1 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white">Onscope Finance Reports</h1>
        <p class="text-gray-600 dark:text-gray-300">Generate and export financial reports</p>
    </div>

    <!-- Date Filter -->
    <div class="p-6 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-700">
        <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Filter Reports</h2>
        <div class="items-end space-y-4 md:space-y-0 md:flex md:space-x-4">
            <div class="md:flex-1">
                <label for="start_date" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="md:flex-1">
                <label for="end_date" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Report Sections -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <!-- Expense Summary -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Expense Summary</h3>
            <div class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">
                ₨<?php echo number_format($totalExpense, 2); ?>
            </div>
            <p class="mb-4 text-gray-600 dark:text-gray-300">Total Expenses for selected period</p>
            <button id="exportExpenseBtn" class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Export as PDF
            </button>
        </div>

        <!-- Petty Cash Summary -->
        <div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-700">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Petty Cash Summary</h3>
            <div class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">
                ₨<?php echo number_format($totalPettyCash, 2); ?>
            </div>
            <p class="mb-4 text-gray-600 dark:text-gray-300">Total Petty Cash for selected period</p>
            <button id="exportPettyCashBtn" class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Export as PDF
            </button>
        </div>
    </div>

    <!-- Report Preview Section -->
    <div id="reportPreview" class="hidden p-6 mt-8 bg-white rounded-lg shadow-md dark:bg-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Report Preview</h2>
            <button id="printReportBtn" class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                Print Report
            </button>
        </div>
        <div id="reportContent" class="prose dark:prose-invert max-w-none">
            <!-- Report content will be inserted here -->
        </div>
    </div>

    <!-- Alerts -->
    <div id="successAlert" class="fixed hidden p-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded shadow-md bottom-4 right-4 dark:bg-green-900 dark:text-green-200" role="alert">
        <p class="font-bold">Success!</p>
        <p id="successMessage">PDF report has been generated and downloaded.</p>
    </div>

    <div id="errorAlert" class="fixed hidden p-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded shadow-md bottom-4 right-4 dark:bg-red-900 dark:text-red-200" role="alert">
        <p class="font-bold">Error!</p>
        <p id="errorMessage">Something went wrong generating the PDF. Please try again.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/reports.js"></script>