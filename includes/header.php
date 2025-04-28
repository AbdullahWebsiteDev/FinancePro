<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? "Onscope Finance - {$pageTitle}" : "Onscope Finance"; ?></title>
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
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="z-10 bg-white shadow-sm dark:bg-gray-800">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button id="mobileSidebarToggle" class="p-2 rounded-md lg:hidden">
                                <i class="text-gray-500 fas fa-bars dark:text-gray-300"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="relative">
                                <button id="userMenuButton" class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none">
                                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <i class="text-xl fas fa-user-circle"></i>
                                </button>
                                
                                <div id="userDropdown" class="absolute right-0 hidden w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
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
            <main class="flex-1 p-4 overflow-auto sm:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
