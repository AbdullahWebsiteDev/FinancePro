<aside id="sidebar" class="bg-indigo-800 dark:bg-gray-900 text-white w-64 flex-shrink-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out fixed lg:static inset-y-0 left-0 z-50">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 bg-indigo-900 dark:bg-gray-800">
            <h1 class="text-xl font-bold">Finance Dashboard</h1>
            <button id="darkModeToggle" class="p-1 rounded-full hover:bg-indigo-700 dark:hover:bg-gray-700">
                <i id="darkModeIcon" class="fas fa-moon"></i>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="dashboard.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Expenses -->
            <a href="expense.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'expense.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-shopping-cart w-6"></i>
                <span>Expenses</span>
            </a>
            
            <!-- Petty Cash -->
            <a href="petty-cash.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'petty-cash.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-cash-register w-6"></i>
                <span>Petty Cash</span>
            </a>
            
            <!-- Reports -->
            <a href="reports.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-chart-bar w-6"></i>
                <span>Reports</span>
            </a>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- User Management (Admin only) -->
            <a href="users.php" class="flex items-center px-4 py-2 rounded-md <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'bg-indigo-700 dark:bg-gray-700' : 'hover:bg-indigo-700 dark:hover:bg-gray-700'; ?>">
                <i class="fas fa-users w-6"></i>
                <span>User Management</span>
            </a>
            <?php endif; ?>
        </nav>
        
        <!-- Logout -->
        <div class="p-4 border-t border-indigo-700 dark:border-gray-700">
            <a href="logout.php" class="flex items-center px-4 py-2 rounded-md hover:bg-indigo-700 dark:hover:bg-gray-700">
                <i class="fas fa-sign-out-alt w-6"></i>
                <span>Sign Out</span>
            </a>
        </div>
    </div>
</aside>
