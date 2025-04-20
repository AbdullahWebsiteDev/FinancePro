<?php
/**
 * API endpoint for Expense operations
 */
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Process POST request for adding or updating expenses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Check action parameter
    $action = $_GET['action'] ?? '';
    
    if ($action === 'add') {
        // Validate required fields
        if (empty($data['date']) || empty($data['item']) || !isset($data['cost']) || $data['cost'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields. Cost must be greater than zero.']);
            exit();
        }
        
        // Add new expense
        $expense_id = addExpense($data['date'], $data['item'], $data['cost']);
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Expense added successfully',
            'expense' => [
                'id' => $expense_id,
                'date' => $data['date'],
                'item' => $data['item'],
                'cost' => (float) $data['cost']
            ]
        ]);
        exit();
    } else if ($action === 'update') {
        // Validate required fields
        if (empty($data['id']) || empty($data['date']) || empty($data['item']) || !isset($data['cost']) || $data['cost'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields. Cost must be greater than zero.']);
            exit();
        }
        
        // Update expense
        $result = updateExpense($data['id'], $data['date'], $data['item'], $data['cost']);
        
        if ($result) {
            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Expense updated successfully',
                'expense' => [
                    'id' => $data['id'],
                    'date' => $data['date'],
                    'item' => $data['item'],
                    'cost' => (float) $data['cost']
                ]
            ]);
        } else {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Expense not found or could not be updated']);
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
        // Get expense ID
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Expense ID is required']);
            exit();
        }
        
        // Delete expense
        $result = deleteExpense($id);
        
        if ($result) {
            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Expense deleted successfully'
            ]);
        } else {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Expense not found or could not be deleted']);
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
        // Get all expenses
        $expenses = getAllExpenses();
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'expenses' => $expenses]);
        exit();
    } else if ($action === 'getRecent') {
        // Get limit parameter (default to 5)
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
        
        // Get recent expenses
        $expenses = getRecentExpenses($limit);
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'expenses' => $expenses]);
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
