<?php
/**
 * Authentication functions for finance dashboard
 */

if (!function_exists('initializeSessionStorage')) {
    require_once 'database.php';
}

/**
 * Attempts to login a user
 * 
 * @param string $username The username
 * @param string $password The password
 * @return array Result of login attempt with success flag and message
 */
function login($username, $password) {
    $users = $_SESSION['db_users'];
    
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'user_id' => $user['id'],
                    'role' => $user['role']
                ];
            }
            return [
                'success' => false,
                'message' => 'Invalid password'
            ];
        }
    }
    
    return [
        'success' => false,
        'message' => 'Username not found'
    ];
}

/**
 * Check if the current user has admin role
 * 
 * @return boolean True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
