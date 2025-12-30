<?php
require_once __DIR__ . '/config.php';
session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$sessionId = session_id(); // server-side session identifier

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - <?= APP_NAME ?></title>
</head>
<body>
  <h2>Dashboard</h2>
  <p>Hello, <b><?= htmlspecialchars($user['full_name']) ?></b></p>
  <p>Role: <b><?= htmlspecialchars($user['role']) ?></b></p>

  <p><b>Session ID (for demo):</b> <?= htmlspecialchars($sessionId) ?></p>
<p style="font-size: 0.9em;">
  CIA mapping: Session ID is <b>Confidentiality</b> sensitive. If stolen, it can enable session hijacking.
</p>

  <hr>
  <h3>CIA Mapping (for students)</h3>
  <ul>
    <li><b>Confidentiality:</b> session cookie (identity), user profile data</li>
    <li><b>Integrity:</b> role shown here must be server-trusted (not user-controlled)</li>
    <li><b>Availability:</b> page depends on server + session + PHP runtime</li>
  </ul>

  <p><a href="profile.php">Profile</a> | <a href="logout.php">Logout</a></p>

</body>
</html>
