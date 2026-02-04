<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";
require_role(['admin','staff']);
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$id = (int)($_GET['id'] ?? 1);

$conn = db();
$stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
?>
<h3>Admin/Staff: User Lookup</h3>
<p>Mode: <b><?= htmlspecialchars($mode) ?></b> | <a href="../mode.php">Switch</a> | <a href="dashboard.php">Dashboard</a></p>

<form method="GET">
  <label>User id: <input name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"></label>
  <button>Lookup</button>
</form>

<?php if ($row): ?>
<ul>
  <li>id: <?= (int)$row['id'] ?></li>
  <li>username: <?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>role: <?= htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>created_at: <?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></li>
</ul>
<?php else: ?><p>No user found.</p><?php endif; ?>
