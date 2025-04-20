<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database functions
require_once 'includes/database.php';

echo "<h1>Database Connection Check</h1>";

// Test database connection
$pdo = getDbConnection();

if ($pdo) {
    echo "<p style='color: green'>Database connection successful!</p>";
    
    // Test querying the users table
    try {
        $stmt = $pdo->query("SELECT * FROM users LIMIT 3");
        $users = $stmt->fetchAll();
        
        echo "<h2>Users Test Query:</h2>";
        echo "<pre>";
        print_r($users);
        echo "</pre>";
        
        // Test querying the expenses table
        $stmt = $pdo->query("SELECT * FROM expenses LIMIT 3");
        $expenses = $stmt->fetchAll();
        
        echo "<h2>Expenses Test Query:</h2>";
        echo "<pre>";
        print_r($expenses);
        echo "</pre>";
        
        // Test querying the petty_cash table
        $stmt = $pdo->query("SELECT * FROM petty_cash LIMIT 3");
        $pettyCash = $stmt->fetchAll();
        
        echo "<h2>Petty Cash Test Query:</h2>";
        echo "<pre>";
        print_r($pettyCash);
        echo "</pre>";
    } catch (PDOException $e) {
        echo "<p style='color: red'>Error running test queries: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red'>Database connection failed! Check your DATABASE_URL environment variable.</p>";
    
    // Show the environment variables (excluding password)
    echo "<h2>Environment Variables:</h2>";
    echo "<ul>";
    echo "<li>PGHOST: " . (getenv('PGHOST') ? "Set" : "Not set") . "</li>";
    echo "<li>PGPORT: " . (getenv('PGPORT') ? getenv('PGPORT') : "Not set") . "</li>";
    echo "<li>PGUSER: " . (getenv('PGUSER') ? "Set" : "Not set") . "</li>";
    echo "<li>PGDATABASE: " . (getenv('PGDATABASE') ? "Set" : "Not set") . "</li>";
    echo "<li>DATABASE_URL: " . (getenv('DATABASE_URL') ? "Set" : "Not set") . "</li>";
    echo "</ul>";
}
?>