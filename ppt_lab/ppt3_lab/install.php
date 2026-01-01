<?php
require_once __DIR__ . '/db.php';
$r=db_root();
$r->exec("CREATE DATABASE IF NOT EXISTS ".DB_NAME);
$r->exec("USE ".DB_NAME);
$r->exec("CREATE TABLE IF NOT EXISTS users(
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE,
 password_hash VARCHAR(255),
 failed_attempts INT DEFAULT 0,
 locked_until DATETIME NULL
)");
$r->exec("CREATE TABLE IF NOT EXISTS audit(
 id INT AUTO_INCREMENT PRIMARY KEY,
 ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 actor VARCHAR(50),
 event VARCHAR(50),
 details TEXT
)");
echo "<h3>Installed</h3><a href='register.php'>Register</a>";
