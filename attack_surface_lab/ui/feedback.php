<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$u = current_user();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? $u['username']);
  $message = trim($_POST['message'] ?? '');

  if ($message === '') {
    $msg = "Message is required.";
  } else {
    $conn = db();
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, name, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $u['id'], $name, $message);
    $stmt->execute();
    $msg = "Saved.";
  }
}

$conn = db();
$res = $conn->query("SELECT name, message, created_at FROM feedback ORDER BY id DESC LIMIT 8");
?>
<h3>Feedback</h3>
<p>Mode: <b><?= htmlspecialchars($mode) ?></b> | <a href="../mode.php">Switch</a> | <a href="dashboard.php">Dashboard</a></p>

<form method="POST">
  <label>Name: <input name="name" value="<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>"></label><br><br>
  <label>Message:<br><textarea name="message" rows="4" cols="70"></textarea></label><br><br>
  <button>Submit</button>
</form>

<?php if ($msg): ?><p><b><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></b></p><?php endif; ?>

<h4>Recent feedback</h4>
<?php while ($r = $res->fetch_assoc()): ?>
  <?php if ($mode === 'VULNERABLE'): ?>
    <p><b><?= $r['name'] ?></b>: <?= $r['message'] ?> <span style="color:#666">(<?= htmlspecialchars($r['created_at']) ?>)</span></p>
  <?php else: ?>
    <p><b><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></b>:
       <?= htmlspecialchars($r['message'], ENT_QUOTES, 'UTF-8') ?>
       <span style="color:#666">(<?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?>)</span>
    </p>
  <?php endif; ?>
<?php endwhile; ?>
