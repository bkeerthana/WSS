<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
header("Content-Type: text/html; charset=UTF-8");
$mode = lab_mode();
$u = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Attack Surface Lab</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; }
    .top { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
    .pill { display:inline-block; padding:4px 10px; border-radius:999px; background:#f2f2f2; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:16px; margin-top:16px; }
    .card { border:1px solid #ddd; border-radius:10px; padding:14px; }
    a { text-decoration:none; }
    code { background:#f7f7f7; padding:2px 6px; border-radius:6px; }
    .small { color:#555; font-size: 0.95em; }
    .btn { display:inline-block; padding:8px 12px; border:1px solid #ccc; border-radius:10px; background:#fafafa; }
  </style>
</head>
<body>
  <div class="top">
    <div>
      <h2 style="margin:0">Attack Surface Lab (Realistic Login)</h2>
      <p class="small" style="margin:6px 0 0 0">Login → personal home → profile → feedback, with mode-based teaching demos.</p>
      <?php if ($u): ?>
        <p class="small" style="margin:6px 0 0 0">Logged in as <b><?= htmlspecialchars($u['username']) ?></b> (<?= htmlspecialchars($u['role']) ?>)</p>
      <?php else: ?>
        <p class="small" style="margin:6px 0 0 0">Not logged in.</p>
      <?php endif; ?>
    </div>
    <div>
      <span class="pill">Mode: <b><?= htmlspecialchars($mode) ?></b></span>
      <a class="btn" href="mode.php" style="margin-left:10px">Switch Mode</a>
      <?php if ($u): ?>
        <a class="btn" href="ui/dashboard.php" style="margin-left:10px">Dashboard</a>
        <a class="btn" href="ui/logout.php" style="margin-left:10px">Logout</a>
      <?php else: ?>
        <a class="btn" href="ui/login.php" style="margin-left:10px">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid">
    <div class="card">
      <h3>GUI + Forms</h3>
      <ul>
        <li><a href="ui/login.php">Login</a> | <a href="ui/register.php">Register</a></li>
        <li><a href="ui/dashboard.php">Dashboard</a></li>
        <li><a href="ui/my_profile.php">My Profile</a></li>
        <li><a href="ui/admin_user_lookup.php?id=1">Admin/Staff user lookup</a></li>
        <li><a href="ui/search.php?q=test">Search</a></li>
        <li><a href="ui/feedback.php">Feedback</a></li>
      </ul>
    </div>

    <div class="card">
      <h3>API</h3>
      <ul>
        <li><a href="api/status.php">GET /api/status.php</a></li>
        <li><a href="api/users.php">GET /api/users.php (X-API-Key)</a></li>
      </ul>
      <p class="small">API key: <code>lab123</code></p>
    </div>

    <div class="card">
      <h3>File Upload</h3>
      <ul>
        <li><a href="upload/upload.php">Upload endpoint</a></li>
      </ul>
      <p class="small">Uploads require login.</p>
    </div>

    <div class="card">
      <h3>Setup</h3>
      <ul>
        <li><a href="setup.php">Run setup</a></li>
      </ul>
    </div>
  </div>

  <hr>
  <p class="small">Ports demo: <code>netstat -ano | findstr LISTENING</code>.</p>
</body>
</html>
