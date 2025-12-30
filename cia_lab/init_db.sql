CREATE DATABASE IF NOT EXISTS cia_lab_db;
USE cia_lab_db;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Passwords (for demo):
-- alice / Alice@123
-- bob   / Bob@123
-- admin / Admin@123
INSERT INTO users (username, password_hash, full_name, role) VALUES
('alice',  '$2y$10$Z0k4O5x4e5h8l3V9r5m0e.7l7c7dKQdGzE8x9i8dYpB1Wq8m0vU3S', 'Alice Kumar', 'user'),
('bob',    '$2y$10$wVYxq8rQxwQh3jH0c8lQxOqg2vH9o2d9m7c1x2p4q6o8y9z1a2b3c', 'Bob Iyer',   'user'),
('admin',  '$2y$10$WQWm8n5tQv5h0zq9m3XlF.u6i3s7GqZQ3cGk8kzvU6p4aWf0u0a0e', 'Admin User', 'admin');

-- NOTE:
-- The above hashes are placeholders. For correctness, generate hashes via the helper below in README,
-- or replace using the provided PHP helper command (recommended).
