<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
header("Content-Type: text/html; charset=UTF-8");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = (string)($_POST['password'] ?? '');

  if ($u === '' || $p === '') {
    $msg = "Username and password are required.";
  } elseif (login_user($u, $p)) {
    header("Location: /wss/attack_surface_lab/ui/dashboard.php");
    exit;
  } else {
    $msg = "Invalid credentials.";
  }
}
$mode = lab_mode();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title>
<style>body{font-family:Arial,sans-serif;margin:24px}.card{border:1px solid #ddd;border-radius:10px;padding:16px;max-width:560px}</style>
</head><body>
<div class="card">
  <h2>Login</h2>
  <p>Mode: <b><?= htmlspecialchars($mode) ?></b> | <a href="../mode.php">Switch mode</a> | <a href="../index.php">Home</a> | <a href="register.php">Register</a></p>

  <form method="POST">
    <label>Username: <input name="username" autocomplete="username"></label><br><br>
    <label>Password: <input name="password" type="password" autocomplete="current-password"></label><br><br>
    <button>Login</button>
  </form>

  <?php if ($msg): ?><p><b><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></b></p><?php endif; ?>

  <hr>
  <p><b>Demo credentials</b></p>
  <ul>
    <li>anu / anu@123</li>
    <li>ruban / ruban@123</li>
    <li>keerthana / keerthana@123</li>
  </ul>
</div>
</body></html>
