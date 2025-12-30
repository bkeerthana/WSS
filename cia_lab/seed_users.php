<?php
require_once __DIR__ . '/db.php';

$users = [
  ['alice', 'Alice@123', 'Alice Kumar', 'user'],
  ['bob', 'Bob@123', 'Bob Iyer', 'user'],
  ['admin', 'Admin@123', 'Admin User', 'admin'],
];

foreach ($users as $u) {
  [$username, $pass, $name, $role] = $u;
  $hash = password_hash($pass, PASSWORD_DEFAULT);

  $stmt = db()->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$username, $hash, $name, $role]);
}

echo "Seeded users successfully. Now DELETE seed_users.php for safety.";
