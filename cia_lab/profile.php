<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user']['id'];

// CIA: profile data is confidentiality-sensitive; fetch from DB server-side
// $stmt = db()->prepare("SELECT username, full_name, role, created_at FROM users WHERE id = ?");
$stmt = db()->prepare("SELECT username, full_name, role, account_balance, created_at FROM users WHERE id = ?");

$stmt->execute([$userId]);
$row = $stmt->fetch();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Profile - <?= APP_NAME ?></title>
</head>
<body>
  <h2>Profile</h2>

  <?php if (!$row): ?>
    <p>User not found.</p>
  <?php else: ?>
    <ul>
      <li><b>Username:</b> <?= htmlspecialchars($row['username']) ?></li>
      <li><b>Full name:</b> <?= htmlspecialchars($row['full_name']) ?></li>
      <li><b>Role:</b> <?= htmlspecialchars($row['role']) ?></li>
      <li><b>Created at:</b> <?= htmlspecialchars($row['created_at']) ?></li>
      <li><b>Account balance:</b> <?= htmlspecialchars($row['account_balance']) ?></li>
    </ul>
  <?php endif; ?>

  <hr>
  <p><b>CIA Questions:</b></p>
  <ol>
    <li>Which fields require <b>Confidentiality</b>?</li>
    <li>Which field requires strong <b>Integrity</b>?</li>
    <li>What happens if this page is not reachable (<b>Availability</b>)?</li>
    <p><b>CIA Mapping for Balance:</b></p>
<ul>
  <li><b>Confidentiality:</b> balance is private financial data</li>
  <li><b>Integrity:</b> balance must not be modified by unauthorized users</li>
</ul>
  </ol>

  <p><a href="dashboard.php">Back</a></p>
</body>
</html>
