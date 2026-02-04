<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");
$q = $_GET['q'] ?? '';
$mode = lab_mode();
?>
<h3>Search (GET Surface)</h3>
<p>Mode: <b><?= htmlspecialchars($mode) ?></b> | <a href="../mode.php">Switch</a> | <a href="dashboard.php">Dashboard</a></p>

<form method="GET">
  <input name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
  <button>Search</button>
</form>

<p>Server received q = <code><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></code></p>
