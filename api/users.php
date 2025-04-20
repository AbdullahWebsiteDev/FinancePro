<?php
/**
 * API endpoint for User Management operations
 */
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Process POST request for adding or updating users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Check action parameter
    $action = $_GET['action'] ?? '';
    
    if ($action === 'add') {
        // Validate required fields
        if (empty($data['username']) || empty($data['password']) || empty($data['role'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields.']);
            exit();
        }
        
        // Validate role value
        if ($data['role'] !== 'admin' && $data['role'] !== 'user') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid role value.']);
            exit();
        }
        
        // Add new user
        $result = addUser($data['username'], $data['password'], $data['role']);
        
        if ($result['success']) {
            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'User added successfully',
                'user' => [
                    'id' => $result['user_id'],
                    'username' => $data['username'],
                    'role' => $data['role']
                ]
            ]);
        } else {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        exit();
    } else if ($action === 'update') {
        // Validate required fields
        if (empty($data['id']) || empty($data['username']) || empty($data['role'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields.']);
            exit();
        }
        
        // Validate role value
        if ($data['role'] !== 'admin' && $data['role'] !== 'user') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid role value.']);
            exit();
        }
        
        // Prevent self-demotion from admin to regular user
        if ($_SESSION['user_id'] == $data['id'] && $data['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You cannot demote yourself from admin role.']);
            exit();
        }
        
        // Update user
        $result = updateUser($data['id'], $data['username'], $data['password'], $data['role']);
        
        if ($result['success']) {
            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'role' => $data['role']
                ]
            ]);
        } else {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        exit();
    } else {
        // Invalid action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Check action parameter
    $action = $_GET['action'] ?? '';
    
    if ($action === 'delete') {
        // Get user ID
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit();
        }
        
        // Prevent self-deletion
        if ($_SESSION['user_id'] == $id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            exit();
        }
        
        // Delete user
        $result = deleteUser($id);
        
        if ($result) {
            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found or could not be deleted']);
        }
        exit();
    } else {
        // Invalid action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getAll') {
        // Get all users
        $users = getAllUsers();
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'users' => $users]);
        exit();
    } else {
        // Invalid action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
} else {
    // Method not allowed
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}
