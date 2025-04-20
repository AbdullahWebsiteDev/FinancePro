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
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user) {
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
        
        return [
            'success' => false,
            'message' => 'Username not found'
        ];
    } catch (PDOException $e) {
        error_log('Error during login: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred'
        ];
    }
}

/**
 * Check if the current user has admin role
 * 
 * @return boolean True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
