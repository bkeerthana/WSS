<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../config.php";
header("Content-Type: text/html; charset=UTF-8");

require_login();
$u = current_user();
$msg = isset($_GET["msg"]) ? (string)$_GET["msg"] : "";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <style>
    body{font-family:Arial,sans-serif;margin:24px}
    .card{max-width:720px;border:1px solid #ddd;border-radius:10px;padding:16px}
    .danger{background:#ffeaea;border:1px solid #d33;padding:10px;border-radius:8px;margin-bottom:12px}
    .ok{color:#0b6}
    code{background:#f6f6f6;padding:2px 6px;border-radius:6px}
  </style>
</head>
<body>
<div class="card">
  <?php if (lab_mode()): ?>
    <div class="danger"><b>LAB MODE</b> — Scenario: <b><?= htmlspecialchars(lab_scenario(), ENT_QUOTES, "UTF-8") ?></b></div>
  <?php endif; ?>

  <h2>Dashboard</h2>
  <?php if ($msg): ?><p class="ok"><b><?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?></b></p><?php endif; ?>

  <p>Logged in as: <b><?= htmlspecialchars($u["username"] ?? "?", ENT_QUOTES, "UTF-8") ?></b></p>
  <p>Role: <code><?= htmlspecialchars($u["role"] ?? "student", ENT_QUOTES, "UTF-8") ?></code></p>

  <p>
    <a href="/wss/attack_surface_auth_lab/ui/logout.php">Logout</a>
    <?php if (lab_mode()): ?>
      | <a href="/wss/attack_surface_auth_lab/ui/lab_toggle.php">To Toggle</a>
    <?php endif; ?>
  </p>
</div>
</body>
</html>
