<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$u = current_user();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title>
<style>body{font-family:Arial,sans-serif;margin:24px}.pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#f2f2f2}</style>
</head><body>
<h2>Personal Home</h2>
<p>Logged in as: <b><?= htmlspecialchars($u['username']) ?></b> (role: <?= htmlspecialchars($u['role']) ?>)</p>
<p><span class="pill">Mode: <b><?= htmlspecialchars($mode) ?></b></span> <a href="../mode.php">Switch mode</a></p>

<ul>
  <li><a href="my_profile.php">My Profile</a></li>
  <li><a href="feedback.php">Feedback</a></li>
  <li><a href="../upload/upload.php">Upload</a></li>
  <?php if (in_array(strtolower($u['role']), ['admin','staff'], true)): ?>
    <li><a href="admin_user_lookup.php?id=1">Admin/Staff: User Lookup</a></li>
  <?php endif; ?>
  <li><a href="../index.php">Home</a></li>
  <li><a href="profile_view.php?id=1">Profile View (BOLA Demo)</a></li>
  <li><a href="admin_panel.php">Admin Panel (RBAC Demo)</a></li> 
  <li><a href="admin_panel.php?as=admin">Admin Panel (VULNERABLE shortcut)</a></li>
  <li><a href="checkout.php">Checkout (Parameter Tampering Demo)</a></li>

  <li><a href="logout.php">Logout</a></li>
</ul>
</body></html>
