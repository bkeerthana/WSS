<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");

$u = current_user();
$conn = db();
$stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $u['id']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
?>
<h3>My Profile</h3>
<p><a href="dashboard.php">Back</a></p>
<ul>
  <li>id: <?= (int)$row['id'] ?></li>
  <li>username: <?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>role: <?= htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>created_at: <?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></li>
</ul>
