<?php
require_once __DIR__ . '/config.php';
session_name(SESSION_NAME);
session_start();

$loggedIn = isset($_SESSION['user']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= APP_NAME ?></title>
</head>
<body>
  <h2><?= APP_NAME ?></h2>

  <?php if ($loggedIn): ?>
    <p>Welcome, <b><?= htmlspecialchars($_SESSION['user']['full_name']) ?></b> (<?= htmlspecialchars($_SESSION['user']['role']) ?>)</p>
    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  <?php else: ?>
    <p>You are not logged in.</p>
    <a href="login.php">Login</a>
  <?php endif; ?>

  <hr>
  <h3>CIA Lab Pointers</h3>
  <ul>
    <li><b>Confidentiality:</b> passwords, sessions, profile data</li>
    <li><b>Integrity:</b> role, identity, server-side decisions</li>
    <li><b>Availability:</b> login/dashboard pages, DB connectivity</li>
  </ul>
</body>
</html>
