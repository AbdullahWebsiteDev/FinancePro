<?php
/**
 * API endpoint for Budget operations
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

// Process POST request for adding or updating budgets
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Check action parameter
    $action = $_GET['action'] ?? '';
    
    if ($action === 'add') {
        // Validate required fields
        if (empty($data['date']) || empty($data['name']) || !isset($data['amount']) || $data['amount'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields. Amount must be greater than zero.']);
            exit();
        }
        
        // Add new budget
        $budget_id = addBudget($data['date'], $data['name'], $data['amount']);
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Budget added successfully',
            'budget' => [
                'id' => $budget_id,
                'date' => $data['date'],
                'name' => $data['name'],
                'amount' => (float) $data['amount']
            ]
        ]);
        exit();
    } else {
        // Invalid action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests - for potential future functionality
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getAll') {
        // Get all budgets
        $budgets = getAllBudgets();
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'budgets' => $budgets]);
        exit();
    } else if ($action === 'getRecent') {
        // Get limit parameter (default to 5)
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
        
        // Get recent budgets
        $budgets = getRecentBudgets($limit);
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'budgets' => $budgets]);
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
