<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: login.php?err=Please+login");
    exit;
}

$pdo = db();
$stmt = $pdo->prepare("SELECT username, marks FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>McCumber Demo - Dashboard</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($row['username']); ?></h2>
    <p>Your marks: <b><?php echo (int)$row['marks']; ?></b></p>

    <p class="small">
      Data State mapping:
      <br>• At Rest: marks in DB
      <br>• In Transit: HTTP response to browser
      <br>• In Processing: server fetching & rendering
    </p>

    <p><a href="logout.php">Logout</a></p>
  </div>
</body>
</html>
