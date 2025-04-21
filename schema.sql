
-- Create expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    item VARCHAR(255) NOT NULL,
    cost DECIMAL(10, 2) NOT NULL
);

-- Create petty_cash table
CREATE TABLE IF NOT EXISTS petty_cash (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

-- Insert default users (password is 'password')
INSERT INTO users (username, password, role) VALUES 
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample data
INSERT INTO expenses (date, item, cost) VALUES 
    (CURRENT_DATE, 'Office Rent', 1200),
    (CURRENT_DATE - INTERVAL '1 day', 'Internet Bill', 75),
    (CURRENT_DATE - INTERVAL '2 days', 'Coffee Machine', 150);

INSERT INTO petty_cash (date, title, amount) VALUES 
    (CURRENT_DATE, 'Office Lunch', 85),
    (CURRENT_DATE - INTERVAL '3 days', 'Taxi Fare', 25);
