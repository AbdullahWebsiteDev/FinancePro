<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set a test cookie to verify cookie functionality
setcookie('test_cookie', 'test_value', time() + 3600, '/');

session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        $result = login($username, $password);
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $result['role'];
            
            // Debug output
            echo "<!-- Login successful. Setting session variables -->";
            echo "<!-- Session ID: " . session_id() . " -->";
            echo "<!-- Session Data: " . print_r($_SESSION, true) . " -->";
            
            // Save session before redirecting
            session_write_close();
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard - Login</title>
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
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                <div class="absolute top-4 right-4">
                    <button id="darkModeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <i id="darkModeIcon" class="fas fa-moon"></i>
                    </button>
                </div>
                
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Finance Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-300">Sign in to access your account</p>
                </div>
                
                <?php if ($error): ?>
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Username</label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required 
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" required 
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign in
                        </button>
                    </div>
                </form>
                
                <div class="mt-6">
                    <p class="text-center text-sm text-gray-600 dark:text-gray-300">
                        <span>Demo Credentials:</span><br>
                        <span>Admin: admin / password</span><br>
                        <span>User: user / password</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;
        
        // Check for saved theme preference or use system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Apply the theme on page load
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            htmlElement.classList.add('dark');
            darkModeIcon.classList.remove('fa-moon');
            darkModeIcon.classList.add('fa-sun');
        }
        
        // Toggle dark mode when button is clicked
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                htmlElement.classList.toggle('dark');
                
                // Update icon
                if (htmlElement.classList.contains('dark')) {
                    darkModeIcon.classList.remove('fa-moon');
                    darkModeIcon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    darkModeIcon.classList.remove('fa-sun');
                    darkModeIcon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    </script>
</body>
</html>
