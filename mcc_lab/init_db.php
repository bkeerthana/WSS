<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$pdo = db();

// Tables
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_value VARCHAR(255) NOT NULL,
  marks INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Reset (demo-friendly)
$pdo->exec("TRUNCATE TABLE users;");
$pdo->exec("TRUNCATE TABLE login_attempts;");

// Seed users: one weak password, one stronger
$users = [
  ['alice', '12345', 78],
  ['bob',   'Bob@2026!', 92],
];

$insert = $pdo->prepare("INSERT INTO users (username, password_value, marks) VALUES (?, ?, ?)");
foreach ($users as [$u, $p, $m]) {
    if (SECURE_MODE) {
        $insert->execute([$u, password_hash($p, PASSWORD_DEFAULT), $m]);
    } else {
        $insert->execute([$u, $p, $m]); // insecure: plain-text at rest
    }
}

echo "<h3>Initialized OK</h3>";
echo "<p>SECURE_MODE = " . (SECURE_MODE ? "true" : "false") . "</p>";
echo "<p>Users: alice / 12345, bob / Bob@2026!</p>";
echo '<p><a href="login.php">Go to Login</a></p>';
?>
