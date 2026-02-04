<?php
require_once __DIR__ . "/config.php";
header("Content-Type: text/html; charset=UTF-8");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Attack Surface Auth Lab</title>
  <style>
    body{font-family:Arial,sans-serif;margin:24px}
    .card{max-width:900px;border:1px solid #ddd;border-radius:10px;padding:16px}
    code{background:#f6f6f6;padding:2px 6px;border-radius:6px}
    .btn{display:inline-block;padding:10px 14px;border:1px solid #ccc;border-radius:10px;margin:6px 8px 6px 0;text-decoration:none}
    .danger{background:#ffeaea;border:1px solid #d33;padding:10px;border-radius:8px;margin:12px 0}
  </style>
</head>
<body>
<div class="card">
  <h2>Attack Surface Auth Lab</h2>

  <?php if (lab_mode()): ?>
    <div class="danger"><b>LAB: Authentication</b> — Scenario: <b><?= htmlspecialchars(lab_scenario(), ENT_QUOTES, "UTF-8") ?></b></div>
  <?php endif; ?>

  <a class="btn" href="/wss/attack_surface_auth_lab/install.php"> One-click Install DB</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/login.php"> Open Login</a>
  <!--
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/dashboard.php">3) Open Dashboard</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/lab_toggle.php">Instructor: Toggle</a>
  -->

  <hr>

  <h3>Vulnerable login pages (auto-select scenario)</h3>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/bypass_login.php">BYPASS Login</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/hardcoded_login.php">HARDCODED Login</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/noratelimit_login.php">NO_RATELIMIT Login</a>

  <hr>
  <p><b>Secure mode users:</b> anu/anu@123, ruban/ruban@123</p>
</div>
</body>
</html>
