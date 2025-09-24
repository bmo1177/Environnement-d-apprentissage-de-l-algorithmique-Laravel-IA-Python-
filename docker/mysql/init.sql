-- Initialize database and user for Laravel + Python service

-- Create the main database
CREATE DATABASE IF NOT EXISTS learner_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create the application user
CREATE USER IF NOT EXISTS 'learner_user'@'%' IDENTIFIED BY 'learner_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON learner_db.* TO 'learner_user'@'%';

-- Flush privileges to reload
FLUSH PRIVILEGES;

-- Optional: Create initial tables if you want to test quickly
-- Laravel migrations will usually handle this
-- Example:
-- USE learner_db;
-- CREATE TABLE sample (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100));
