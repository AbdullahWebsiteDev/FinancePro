
<?php
require_once 'includes/database.php';

try {
    // Connect to PostgreSQL without selecting a database
    $pdo = new PDO("pgsql:host=localhost;port=5432", "postgres", "admin");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE finance_db");
    echo "Database created successfully\n";
    
    // Connect to the new database and create tables
    $pdo = getDbConnection();
    if ($pdo) {
        echo "Connected to database successfully\n";
        // Initialize tables
        initDatabase();
        echo "Tables initialized successfully\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
