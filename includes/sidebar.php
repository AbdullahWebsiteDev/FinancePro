<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex-shrink-0 w-64 text-white transition-transform duration-200 ease-in-out transform -translate-x-full bg-indigo-800 dark:bg-gray-900 lg:translate-x-0 lg:static">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 bg-indigo-900 dark:bg-gray-800">
            <h1 class="text-xl font-bold">Onscope Finance</h1>
            <button id="darkModeToggle" class="p-1 rounded-full hover:bg-indigo-700 dark:hover:bg-gray-700">
                <i id="darkModeIcon" class="fas fa-moon"></i>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="dashboard.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="w-6 fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Expenses -->
            <a href="expense.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'expense.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="w-6 fas fa-shopping-cart"></i>
                <span>Expenses</span>
            </a>
            
            <!-- Petty Cash -->
            <a href="petty-cash.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'petty-cash.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="w-6 fas fa-cash-register"></i>
                <span>Petty Cash</span>
            </a>
            
            <!-- Reports -->
            <a href="reports.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="w-6 fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- User Management (Admin only) -->
            <a href="users.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="w-6 fas fa-users"></i>
                <span>User Management</span>
            </a>
            <?php endif; ?>
        </nav>
        
        <!-- Financial Summary -->
        <div class="px-4 py-2 space-y-2">
            <div class="flex items-center justify-between text-gray-300 dark:text-gray-300">
                <span>Total Expenses</span>
                <span>₨<?php echo number_format(getTotalExpense(), 2); ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-300 dark:text-gray-300">
                <span>Total Budget</span>
                <span>₨<?php echo number_format(getTotalBalance(), 2); ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-300 dark:text-gray-300">
                <span>Petty Cash</span>
                <span>₨<?php echo number_format(getTotalPettyCash(), 2); ?></span>
            </div>
        </div>
        
        <!-- Logout -->
        <div class="p-4 border-t border-indigo-700 dark:border-gray-700">
            <a href="logout.php" class="flex items-center px-4 py-2 rounded-md hover:bg-indigo-700 dark:hover:bg-gray-700">
                <i class="w-6 fas fa-sign-out-alt"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </div>
</aside>
