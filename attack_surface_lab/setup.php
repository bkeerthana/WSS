<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/db.php";

header("Content-Type: text/plain; charset=UTF-8");

function must(mysqli $conn, bool $ok, string $step): void {
  if (!$ok) {
    echo "FAILED: $step\n";
    echo "MySQL errno: " . $conn->errno . "\n";
    echo "MySQL error: " . $conn->error . "\n";
    exit;
  }
}

$marker = __DIR__ . "/.setup_done";
if (file_exists($marker)) {
  echo "Setup already completed.\n";
  echo "If you need to re-run: delete .setup_done and run setup again.\n";
  exit;
}

$conn = db_server();

// 1) Create DB
$ok = $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
must($conn, (bool)$ok, "CREATE DATABASE");

// 2) Select DB
$ok = $conn->select_db(DB_NAME);
must($conn, (bool)$ok, "SELECT DATABASE " . DB_NAME);

// 3) Create tables
$ok = $conn->query("
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  role ENUM('student','staff','admin') NOT NULL DEFAULT 'student',
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");
must($conn, (bool)$ok, "CREATE TABLE users");

$ok = $conn->query("
CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(80) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
");
must($conn, (bool)$ok, "CREATE TABLE feedback");

// 4) Upsert demo users (robust approach: try UPDATE first, else INSERT)
function upsert_user(mysqli $conn, int $id, string $username, string $role, string $plainPassword): void {
  $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

  // Try update by username first
  $stmt = $conn->prepare("UPDATE users SET role=?, password_hash=? WHERE username=?");
  if (!$stmt) {
    echo "Prepare failed (UPDATE users): " . $conn->error . "\n";
    exit;
  }
  $stmt->bind_param("sss", $role, $hash, $username);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    return; // updated existing user
  }

  // Insert new user
  $stmt = $conn->prepare("INSERT INTO users (id, username, role, password_hash) VALUES (?, ?, ?, ?)");
  if (!$stmt) {
    echo "Prepare failed (INSERT users): " . $conn->error . "\n";
    exit;
  }
  $stmt->bind_param("isss", $id, $username, $role, $hash);
  $stmt->execute();
}

upsert_user($conn, 1, "anu",       "student", "anu@123");
upsert_user($conn, 2, "ruban",     "staff",   "ruban@123");
upsert_user($conn, 3, "keerthana", "admin",   "keerthana@123");

// 5) Seed feedback safely (ignore if it already exists)
$ok = $conn->query("INSERT INTO feedback (user_id, name, message) VALUES (1,'anu','Hello from Anu (seed).')");
if (!$ok) {
  // Not fatal if duplicate / constraint etc.
  echo "Note: feedback seed insert warning: " . $conn->error . "\n";
}

// 6) Default mode file
set_lab_mode("VULNERABLE");

file_put_contents($marker, "done");

echo "Setup complete.\n";
echo "Open: http://localhost/wss/attack_surface_lab/\n";
echo "Login: http://localhost/wss/attack_surface_lab/ui/login.php\n";
echo "Register: http://localhost/wss/attack_surface_lab/ui/register.php\n";
echo "Demo users:\n";
echo " - anu / anu@123 (student)\n";
echo " - ruban / ruban@123 (staff)\n";
echo " - keerthana / keerthana@123 (admin)\n";
