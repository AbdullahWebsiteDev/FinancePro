<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? "Finance Dashboard - {$pageTitle}" : "Finance Dashboard"; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply transition-colors duration-200;
            }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-800 dark:text-white">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button id="mobileSidebarToggle" class="p-2 rounded-md lg:hidden">
                                <i class="fas fa-bars text-gray-500 dark:text-gray-300"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="relative">
                                <button id="userMenuButton" class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none">
                                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <i class="fas fa-user-circle text-xl"></i>
                                </button>
                                
                                <div id="userDropdown" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 hidden">
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        Sign Out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
