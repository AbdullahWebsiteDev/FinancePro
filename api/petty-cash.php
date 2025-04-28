<?php
/**
 * API endpoint for Petty Cash operations
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

// Process POST request for adding or updating petty cash
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Check action parameter
    $action = $_GET['action'] ?? '';
    
    if ($action === 'add') {
        // Validate required fields
        if (empty($data['date']) || empty($data['title']) || !isset($data['amount']) || $data['amount'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields. Amount must be greater than zero.']);
            exit();
        }
        
        // Add new petty cash record
        $petty_cash_id = addPettyCash($data['date'], $data['title'], $data['amount']);
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Petty cash added successfully',
            'pettyCash' => [
                'id' => $petty_cash_id,
                'date' => $data['date'],
                'title' => $data['title'],
                'amount' => (float) $data['amount']
            ]
        ]);
        exit();
    } else if ($action === 'update') {
        // Validate required fields
        if (empty($data['id']) || empty($data['date']) || empty($data['title']) || !isset($data['amount']) || $data['amount'] <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please provide all required fields. Amount must be greater than zero.']);
            exit();
        }
        
        // Update petty cash record
        $result = updatePettyCash($data['id'], $data['date'], $data['title'], $data['amount']);
        
        // Return response
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Petty cash updated successfully',
                'pettyCash' => [
                    'id' => $data['id'],
                    'date' => $data['date'],
                    'title' => $data['title'],
                    'amount' => (float) $data['amount']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Petty cash record not found or could not be updated']);
        }
        exit();
    } else {
        // Invalid action
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get petty cash ID
    $id = $_GET['id'] ?? null;

    if ($id) {
        require_once '../includes/database.php';

        if (deletePettyCash($id)) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete the record']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid ID provided']);
    }
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getAll') {
        // Get all petty cash records
        $petty_cash = getAllPettyCash();
        
        // Return response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'pettyCash' => $petty_cash]);
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
