<?php
/**
 * Database functions for finance dashboard
 * 
 * This file contains database connection and related functions
 * for interacting with PostgreSQL database
 */

/**
 * Get a database connection
 */
function getDbConnection() {
    // Get the DATABASE_URL from environment
    $db_url = getenv('DATABASE_URL');
    
    if (!$db_url) {
        // Local database settings
        $host = 'localhost';
        $port = 5432;
        $dbname = 'finance_db';
        $user = 'postgres';
        $password = 'admin'; // Set your PostgreSQL password here
        
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
        
        // Test if PostgreSQL is running
        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$socket) {
            error_log("PostgreSQL server not accessible: $errstr ($errno)");
            return null;
        }
        fclose($socket);
        
        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log('Local database connection error: ' . $e->getMessage());
            return null;
        }
    }
    
    try {
        // Parse the DATABASE_URL to extract components
        $url = parse_url($db_url);
        
        $host = $url['host'];
        $port = $url['port'] ?? 5432;
        $dbname = ltrim($url['path'], '/');
        $user = $url['user'];
        $password = $url['pass'];
        
        // Build the DSN for PostgreSQL
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
        
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Initialize the database tables if they don't exist
 */
function initDatabase() {
    try {
        $pdo = getDbConnection();
        
        if (!$pdo) {
            return false;
        }
        
        // Create expenses table
        $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
            id SERIAL PRIMARY KEY,
            date DATE NOT NULL,
            item VARCHAR(255) NOT NULL,
            cost DECIMAL(10, 2) NOT NULL
        )");
        
        // Create petty_cash table
        $pdo->exec("CREATE TABLE IF NOT EXISTS petty_cash (
            id SERIAL PRIMARY KEY,
            date DATE NOT NULL,
            title VARCHAR(255) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL
        )");
        
        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user'
        )");
        
        // Check if we need to insert default users
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            // Insert default admin and user
            $pdo->exec("INSERT INTO users (username, password, role) VALUES 
                ('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin'),
                ('user', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'user')
            ");
            
            // Insert sample expenses
            $pdo->exec("INSERT INTO expenses (date, item, cost) VALUES 
                ('" . date('Y-m-d') . "', 'Office Rent', 1200),
                ('" . date('Y-m-d', strtotime('-1 day')) . "', 'Internet Bill', 75),
                ('" . date('Y-m-d', strtotime('-2 day')) . "', 'Coffee Machine', 150)
            ");
            
            // Insert sample petty cash
            $pdo->exec("INSERT INTO petty_cash (date, title, amount) VALUES 
                ('" . date('Y-m-d') . "', 'Office Lunch', 85),
                ('" . date('Y-m-d', strtotime('-3 day')) . "', 'Taxi Fare', 25)
            ");
        }
        
        return true;
    } catch (PDOException $e) {
        error_log('Database initialization error: ' . $e->getMessage());
        return false;
    }
}

// Initialize database
initDatabase();

// Expense functions
function getAllExpenses() {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT * FROM expenses ORDER BY date DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching expenses: ' . $e->getMessage());
        return [];
    }
}

// Helper function to get budget by date range - returns empty array since budget is removed
function getBudgetsByDateRange($startDate, $endDate) {
    return [];
}

function getRecentExpenses($limit = 5) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM expenses ORDER BY date DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching recent expenses: ' . $e->getMessage());
        return [];
    }
}

function addExpense($date, $item, $cost) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO expenses (date, item, cost) VALUES (:date, :item, :cost) RETURNING id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':item', $item);
        $stmt->bindParam(':cost', $cost);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['id'];
    } catch (PDOException $e) {
        error_log('Error adding expense: ' . $e->getMessage());
        return 0;
    }
}

function updateExpense($id, $date, $item, $cost) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("UPDATE expenses SET date = :date, item = :item, cost = :cost WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':item', $item);
        $stmt->bindParam(':cost', $cost);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error updating expense: ' . $e->getMessage());
        return false;
    }
}

function deleteExpense($id) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error deleting expense: ' . $e->getMessage());
        return false;
    }
}

function getExpensesByDateRange($startDate, $endDate) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM expenses WHERE date BETWEEN :start_date AND :end_date ORDER BY date DESC");
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching expenses by date range: ' . $e->getMessage());
        return [];
    }
}

// Petty Cash functions
function getAllPettyCash() {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT * FROM petty_cash ORDER BY date DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching petty cash: ' . $e->getMessage());
        return [];
    }
}

function addPettyCash($date, $title, $amount) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("INSERT INTO petty_cash (date, title, amount) VALUES (:date, :title, :amount) RETURNING id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['id'];
    } catch (PDOException $e) {
        error_log('Error adding petty cash: ' . $e->getMessage());
        return 0;
    }
}

function getPettyCashByDateRange($startDate, $endDate) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM petty_cash WHERE date BETWEEN :start_date AND :end_date ORDER BY date DESC");
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching petty cash by date range: ' . $e->getMessage());
        return [];
    }
}

function deletePettyCash($id) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("DELETE FROM petty_cash WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log('Error deleting petty cash: ' . $e->getMessage());
        return false;
    }
}

function updatePettyCash($id, $date, $title, $amount) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("UPDATE petty_cash SET date = :date, title = :title, amount = :amount WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error updating petty cash: ' . $e->getMessage());
        return false;
    }
}

// User functions
function getAllUsers() {
    try {
        $pdo = getDbConnection();
        // Sort users by role (admin first) then by username
        $stmt = $pdo->query("SELECT * FROM users ORDER BY 
            CASE WHEN role = 'admin' THEN 0 ELSE 1 END, 
            username");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching users: ' . $e->getMessage());
        return [];
    }
}

function addUser($username, $password, $role) {
    try {
        $pdo = getDbConnection();
        
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Insert the new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role) RETURNING id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return ['success' => true, 'user_id' => $result['id']];
    } catch (PDOException $e) {
        error_log('Error adding user: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

function updateUser($id, $username, $password, $role) {
    try {
        $pdo = getDbConnection();
        
        // Check if username already exists for a different user
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id != :id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Update the user
        if (!empty($password)) {
            // Update with new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            // Update without changing password
            $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return ['success' => $stmt->rowCount() > 0];
    } catch (PDOException $e) {
        error_log('Error updating user: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

function deleteUser($id) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error deleting user: ' . $e->getMessage());
        return false;
    }
}

// Dashboard summary functions
function getTotalBalance() {
    try {
        $pdo = getDbConnection();
        
        // Get total expenses
        $stmt = $pdo->query("SELECT COALESCE(SUM(cost), 0) as total FROM expenses");
        $expenseTotal = $stmt->fetch()['total'];
        
        // Get total petty cash
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM petty_cash");
        $pettyCashTotal = $stmt->fetch()['total'];
        
        // Balance is negative expenses plus petty cash
        return (-1 * floatval($expenseTotal)) + floatval($pettyCashTotal);
    } catch (PDOException $e) {
        error_log('Error calculating total balance: ' . $e->getMessage());
        return 0;
    }
}

function getTotalExpense() {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT COALESCE(SUM(cost), 0) as total FROM expenses");
        return floatval($stmt->fetch()['total']);
    } catch (PDOException $e) {
        error_log('Error calculating total expense: ' . $e->getMessage());
        return 0;
    }
}

function getTotalPettyCash() {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM petty_cash");
        return floatval($stmt->fetch()['total']);
    } catch (PDOException $e) {
        error_log('Error calculating total petty cash: ' . $e->getMessage());
        return 0;
    }
}

function getTodayExpense() {
    try {
        $pdo = getDbConnection();
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(cost), 0) as total FROM expenses WHERE date = :today");
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        
        return floatval($stmt->fetch()['total']);
    } catch (PDOException $e) {
        error_log('Error calculating today\'s expense: ' . $e->getMessage());
        return 0;
    }
}
