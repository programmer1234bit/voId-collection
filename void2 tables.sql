-- Complete SQL Schema for void_food Database
-- This script creates all necessary tables with proper relationships and constraints

USE void_food;

-- Drop existing tables if they exist (to handle corrupted tables)
DROP TABLE IF EXISTS payment_logs;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_admin INT DEFAULT 0,
    phone_number VARCHAR(20),
    reset_token TEXT,
    reset_token_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User profiles table
CREATE TABLE IF NOT EXISTS user_profiles (
    user_id INT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    avatar TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menu table
CREATE TABLE IF NOT EXISTS menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    image_url VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    tx_id VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255),
    phone VARCHAR(20),
    full_name VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    items JSON,
    payment_status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment logs table
CREATE TABLE IF NOT EXISTS payment_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reference VARCHAR(255) UNIQUE NOT NULL,
    charge_id VARCHAR(255),
    customer_email VARCHAR(255),
    amount DECIMAL(10, 2),
    currency VARCHAR(10),
    status VARCHAR(50) DEFAULT 'pending',
    payment_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_data TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories if not exist
INSERT IGNORE INTO categories (name) VALUES
('Appetizers'),
('Main Courses'),
('Desserts'),
('Beverages');

-- Create admin user if not exists
INSERT IGNORE INTO users (email, password_hash, is_admin) VALUES
('admin@voidfood.com', '$2y$10$example.hash.for.admin', 1);