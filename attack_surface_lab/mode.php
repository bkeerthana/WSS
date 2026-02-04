<?php
require_once __DIR__ . "/config.php";
header("Content-Type: text/html; charset=UTF-8");

$current = lab_mode();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $target = $_POST['mode'] ?? '';
  if (set_lab_mode($target)) {
    $current = lab_mode();
    $msg = "Mode updated to: " . htmlspecialchars($current, ENT_QUOTES, 'UTF-8');
  } else {
    $msg = "Failed to update mode. Check folder write permissions.";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lab Mode</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; }
    .card { border: 1px solid #ddd; padding: 16px; border-radius: 10px; max-width: 760px; }
    .pill { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #f2f2f2; }
    button { padding: 8px 14px; margin-right: 8px; }
    .hint { color: #444; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Attack Surface Lab: Mode Switch</h2>
    <p>Current mode: <span class="pill"><b><?= htmlspecialchars($current) ?></b></span></p>
    <?php if ($msg): ?><p><b><?= htmlspecialchars($msg) ?></b></p><?php endif; ?>

    <form method="POST">
      <input type="hidden" name="mode" value="<?= $current === 'VULNERABLE' ? 'HARDENED' : 'VULNERABLE' ?>">
      <button type="submit">Switch to <?= $current === 'VULNERABLE' ? 'HARDENED' : 'VULNERABLE' ?></button>
      <a href="index.php">Back to Lab</a>
    </form>

    <hr>
    <p class="hint">
      VULNERABLE: controlled “attack succeeds” behavior for teaching.<br>
      HARDENED: shows mitigations.
    </p>
  </div>
</body>
</html>
