-- Run this in phpMyAdmin or MariaDB to set up the database

CREATE DATABASE IF NOT EXISTS finelines;
USE finelines;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- Employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    date DATETIME NOT NULL,
    quoted_price INT DEFAULT NULL,
    estimated_completion_time TIME DEFAULT NULL,
    approved_by_client BOOLEAN DEFAULT 0,
    assigned_employee INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (assigned_employee) REFERENCES employees(id)
);

-- Sample admin account (password: admin123)
INSERT INTO users (type, first_name, last_name, username, password, email)
VALUES ('admin', 'Bailey', 'Erwin', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'baileyerwin01@gmail.com');

-- Sample employees
INSERT INTO employees (first_name, last_name) VALUES ('Bailey', 'Erwin');
INSERT INTO employees (first_name, last_name) VALUES ('John', 'Smith');
