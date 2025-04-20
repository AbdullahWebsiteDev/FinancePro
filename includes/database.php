<?php
/**
 * Database functions for finance dashboard
 * 
 * Note: For this implementation, we're using session-based storage as a temporary solution
 * In a production environment, this would be replaced with actual MySQL database connections
 */

// Initialize session storage if not already set
function initializeSessionStorage() {
    if (!isset($_SESSION['db_budgets'])) {
        $_SESSION['db_budgets'] = [
            ['id' => 1, 'date' => date('Y-m-d'), 'name' => 'Monthly Budget', 'amount' => 5000],
            ['id' => 2, 'date' => date('Y-m-d', strtotime('-1 week')), 'name' => 'Office Supplies', 'amount' => 500]
        ];
    }
    
    if (!isset($_SESSION['db_expenses'])) {
        $_SESSION['db_expenses'] = [
            ['id' => 1, 'date' => date('Y-m-d'), 'item' => 'Office Rent', 'cost' => 1200],
            ['id' => 2, 'date' => date('Y-m-d', strtotime('-1 day')), 'item' => 'Internet Bill', 'cost' => 75],
            ['id' => 3, 'date' => date('Y-m-d', strtotime('-2 day')), 'item' => 'Coffee Machine', 'cost' => 150]
        ];
    }
    
    if (!isset($_SESSION['db_petty_cash'])) {
        $_SESSION['db_petty_cash'] = [
            ['id' => 1, 'date' => date('Y-m-d'), 'title' => 'Office Lunch', 'amount' => 85],
            ['id' => 2, 'date' => date('Y-m-d', strtotime('-3 day')), 'title' => 'Taxi Fare', 'amount' => 25]
        ];
    }
    
    if (!isset($_SESSION['db_users'])) {
        $_SESSION['db_users'] = [
            ['id' => 1, 'username' => 'admin', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'admin'],
            ['id' => 2, 'username' => 'user', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'user']
        ];
    }
}

// Initialize storage on first load
initializeSessionStorage();

// Budget functions
function getAllBudgets() {
    // Sort by date, most recent first
    $budgets = $_SESSION['db_budgets'];
    usort($budgets, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return $budgets;
}

function getRecentBudgets($limit = 5) {
    $budgets = getAllBudgets();
    return array_slice($budgets, 0, $limit);
}

function addBudget($date, $name, $amount) {
    $id = count($_SESSION['db_budgets']) + 1;
    $budget = [
        'id' => $id,
        'date' => $date,
        'name' => $name,
        'amount' => (float) $amount
    ];
    $_SESSION['db_budgets'][] = $budget;
    return $id;
}

function getBudgetsByDateRange($startDate, $endDate) {
    $filtered = array_filter($_SESSION['db_budgets'], function($budget) use ($startDate, $endDate) {
        $budgetDate = strtotime($budget['date']);
        return $budgetDate >= strtotime($startDate) && $budgetDate <= strtotime($endDate);
    });
    
    usort($filtered, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $filtered;
}

// Expense functions
function getAllExpenses() {
    // Sort by date, most recent first
    $expenses = $_SESSION['db_expenses'];
    usort($expenses, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return $expenses;
}

function getRecentExpenses($limit = 5) {
    $expenses = getAllExpenses();
    return array_slice($expenses, 0, $limit);
}

function addExpense($date, $item, $cost) {
    $id = count($_SESSION['db_expenses']) + 1;
    $expense = [
        'id' => $id,
        'date' => $date,
        'item' => $item,
        'cost' => (float) $cost
    ];
    $_SESSION['db_expenses'][] = $expense;
    return $id;
}

function updateExpense($id, $date, $item, $cost) {
    foreach ($_SESSION['db_expenses'] as $key => $expense) {
        if ($expense['id'] == $id) {
            $_SESSION['db_expenses'][$key]['date'] = $date;
            $_SESSION['db_expenses'][$key]['item'] = $item;
            $_SESSION['db_expenses'][$key]['cost'] = (float) $cost;
            return true;
        }
    }
    return false;
}

function deleteExpense($id) {
    foreach ($_SESSION['db_expenses'] as $key => $expense) {
        if ($expense['id'] == $id) {
            array_splice($_SESSION['db_expenses'], $key, 1);
            return true;
        }
    }
    return false;
}

function getExpensesByDateRange($startDate, $endDate) {
    $filtered = array_filter($_SESSION['db_expenses'], function($expense) use ($startDate, $endDate) {
        $expenseDate = strtotime($expense['date']);
        return $expenseDate >= strtotime($startDate) && $expenseDate <= strtotime($endDate);
    });
    
    usort($filtered, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $filtered;
}

// Petty Cash functions
function getAllPettyCash() {
    // Sort by date, most recent first
    $pettyCash = $_SESSION['db_petty_cash'];
    usort($pettyCash, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return $pettyCash;
}

function addPettyCash($date, $title, $amount) {
    $id = count($_SESSION['db_petty_cash']) + 1;
    $record = [
        'id' => $id,
        'date' => $date,
        'title' => $title,
        'amount' => (float) $amount
    ];
    $_SESSION['db_petty_cash'][] = $record;
    return $id;
}

function getPettyCashByDateRange($startDate, $endDate) {
    $filtered = array_filter($_SESSION['db_petty_cash'], function($record) use ($startDate, $endDate) {
        $recordDate = strtotime($record['date']);
        return $recordDate >= strtotime($startDate) && $recordDate <= strtotime($endDate);
    });
    
    usort($filtered, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $filtered;
}

// User functions
function getAllUsers() {
    $users = $_SESSION['db_users'];
    // Sort by role (admin first) and then by username
    usort($users, function($a, $b) {
        if ($a['role'] === $b['role']) {
            return strcmp($a['username'], $b['username']);
        }
        return $a['role'] === 'admin' ? -1 : 1;
    });
    return $users;
}

function addUser($username, $password, $role) {
    // Check if username already exists
    foreach ($_SESSION['db_users'] as $user) {
        if ($user['username'] === $username) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
    }
    
    $id = count($_SESSION['db_users']) + 1;
    $user = [
        'id' => $id,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role
    ];
    $_SESSION['db_users'][] = $user;
    return ['success' => true, 'user_id' => $id];
}

function updateUser($id, $username, $password, $role) {
    // Check if username already exists for a different user
    foreach ($_SESSION['db_users'] as $user) {
        if ($user['username'] === $username && $user['id'] != $id) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
    }
    
    foreach ($_SESSION['db_users'] as $key => $user) {
        if ($user['id'] == $id) {
            $_SESSION['db_users'][$key]['username'] = $username;
            $_SESSION['db_users'][$key]['role'] = $role;
            
            // Update password only if provided
            if (!empty($password)) {
                $_SESSION['db_users'][$key]['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            return ['success' => true];
        }
    }
    return ['success' => false, 'message' => 'User not found'];
}

function deleteUser($id) {
    foreach ($_SESSION['db_users'] as $key => $user) {
        if ($user['id'] == $id) {
            array_splice($_SESSION['db_users'], $key, 1);
            return true;
        }
    }
    return false;
}

// Dashboard summary functions
function getTotalBalance() {
    $budgetTotal = array_reduce($_SESSION['db_budgets'], function($carry, $item) {
        return $carry + $item['amount'];
    }, 0);
    
    $expenseTotal = array_reduce($_SESSION['db_expenses'], function($carry, $item) {
        return $carry + $item['cost'];
    }, 0);
    
    return $budgetTotal - $expenseTotal;
}

function getTotalExpense() {
    return array_reduce($_SESSION['db_expenses'], function($carry, $item) {
        return $carry + $item['cost'];
    }, 0);
}

function getTotalPettyCash() {
    return array_reduce($_SESSION['db_petty_cash'], function($carry, $item) {
        return $carry + $item['amount'];
    }, 0);
}

function getTodayExpense() {
    $today = date('Y-m-d');
    $todayExpenses = array_filter($_SESSION['db_expenses'], function($expense) use ($today) {
        return $expense['date'] === $today;
    });
    
    return array_reduce($todayExpenses, function($carry, $item) {
        return $carry + $item['cost'];
    }, 0);
}
