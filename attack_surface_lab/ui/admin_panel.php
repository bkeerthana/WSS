<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$u = current_user();
$conn = db();

/**
 * RBAC check:
 * VULNERABLE: trusts client-controlled role (?as=admin)
 * HARDENED: trusts server-side session role ($u['role'])
 */
if ($mode === "VULNERABLE") {
  $as = strtolower((string)($_GET['as'] ?? ''));
  if ($as !== 'admin') {
    http_response_code(403);
    echo "<h3>403 Forbidden</h3>";
    echo "<p>VULNERABLE demo: add <code>?as=admin</code> to simulate a broken role check.</p>";
    echo "<p><a href='dashboard.php'>Back</a></p>";
    exit;
  }
} else {
  $role = strtolower((string)($u['role'] ?? 'student'));
  if (!in_array($role, ['admin'], true)) {
    http_response_code(403);
    echo "<h3>403 Forbidden</h3>";
    echo "<p>HARDENED: Admin role required.</p>";
    echo "<p><a href='dashboard.php'>Back</a></p>";
    exit;
  }
}

/**
 * Admin action: Promote a user (teaching demo)
 */
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $targetId = (int)($_POST['id'] ?? 0);
  $newRole  = strtolower((string)($_POST['role'] ?? 'student'));
  if (!in_array($newRole, ['student','staff','admin'], true)) $newRole = 'student';

  $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
  $stmt->bind_param("si", $newRole, $targetId);
  $stmt->execute();
  $msg = "Updated user id {$targetId} to role {$newRole}.";
}

// List users
$res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
?>
<h3>Admin Panel (RBAC Failure Demo)</h3>
<p>
  Mode: <b><?= htmlspecialchars($mode) ?></b> |
  <a href="../mode.php">Switch</a> |
  <a href="dashboard.php">Dashboard</a>
</p>

<?php if ($mode === "VULNERABLE"): ?>
  <p><b>VULNERABLE:</b> server trusts <code>?as=admin</code> (client-controlled).</p>
  <p>Try: <a href="admin_panel.php?as=admin">Open Admin Panel as “admin”</a></p>
<?php else: ?>
  <p><b>HARDENED:</b> server trusts session role only.</p>
<?php endif; ?>

<?php if ($msg): ?><p style="color:#0b6"><b><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></b></p><?php endif; ?>

<h4>Promote/Demote user</h4>
<form method="POST">
  <label>User id:
    <input name="id" required style="width:80px">
  </label>
  <label>Role:
    <select name="role">
      <option value="student">student</option>
      <option value="staff">staff</option>
      <option value="admin">admin</option>
    </select>
  </label>
  <button>Apply</button>
</form>

<h4>All users</h4>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>id</th><th>username</th><th>role</th><th>created</th></tr>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($r['role'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
  <?php endwhile; ?>
</table>
