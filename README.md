# FinancePro

A web-based financial management system built with PHP that helps track budgets, expenses, and petty cash management.

## Features
- Budget Management
- Expense Tracking
- Petty Cash Management
- User Management
- Report Generation

## Requirements
- PHP 7.1 or higher
- MySQL/MariaDB
- Composer

## Installation
1. Clone this repository
2. Run `composer install`
3. Configure your database settings
4. Import the schema.sql file to your database
5. Configure your web server to point to the project directory

## Deploying to Render
1. Create a new Web Service on Render
2. Select "Docker" as the environment
3. Connect your repository
4. Configure Environment Variables:
   - `MYSQL_HOST`: Your database host
   - `MYSQL_DATABASE`: Your database name
   - `MYSQL_USER`: Your database user
   - `MYSQL_PASSWORD`: Your database password
5. Click "Create Web Service"

The application will be automatically built and deployed using the Dockerfile configuration.