<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$u = current_user();

// Object identifier (target user id)
$targetId = (int)($_GET['id'] ?? $u['id']);

$conn = db();

/**
 * BOLA logic
 * VULNERABLE: no object-level authorization check
 * HARDENED: only allow own profile unless staff/admin
 */
if ($mode === "HARDENED") {
  $role = strtolower($u['role'] ?? 'student');
  $isPrivileged = in_array($role, ['admin','staff'], true);

  if (!$isPrivileged && $targetId !== (int)$u['id']) {
    http_response_code(403);
    echo "<h3>403 Forbidden</h3>";
    echo "<p>HARDENED: You can only view your own profile.</p>";
    echo "<p><a href='dashboard.php'>Back</a></p>";
    exit;
  }
}

// Fetch target user
$stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $targetId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo "<h3>Profile View (BOLA Demo)</h3>";
echo "<p>Mode: <b>" . htmlspecialchars($mode) . "</b> | <a href='../mode.php'>Switch</a> | <a href='dashboard.php'>Dashboard</a></p>";

echo "<form method='GET'>
        <label>User id: <input name='id' value='" . htmlspecialchars((string)$targetId, ENT_QUOTES, 'UTF-8') . "'></label>
        <button>View</button>
      </form>";

if (!$row) {
  echo "<p>No user found for id.</p>";
  exit;
}

echo "<hr>";
echo "<ul>";
echo "<li>id: " . (int)$row['id'] . "</li>";
echo "<li>username: " . htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . "</li>";
echo "<li>role: " . htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8') . "</li>";
echo "<li>created_at: " . htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') . "</li>";
echo "</ul>";

echo "<p><b>Teaching note:</b> In VULNERABLE mode, changing <code>?id=</code> demonstrates BOLA/IDOR.</p>";
