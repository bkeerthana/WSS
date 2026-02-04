<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

header("Content-Type: text/html; charset=UTF-8");

// If already logged in, go to dashboard
if (current_user()) {
  header("Location: /wss/attack_surface_lab/ui/dashboard.php");
  exit;
}

$mode = lab_mode();
$msg = "";
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  $confirm  = (string)($_POST['confirm'] ?? '');

  // VULNERABLE: allow user-controlled role (teaching only).
  // HARDENED: enforce server-side role allowlist (student only).
  $role = ($mode === 'VULNERABLE') ? (string)($_POST['role'] ?? 'student') : 'student';
  $role = strtolower($role);
  if (!in_array($role, ['student','staff','admin'], true)) $role = 'student';

  if ($username === '' || $password === '' || $confirm === '') {
    $err = "All fields are required.";
  } elseif ($password !== $confirm) {
    $err = "Password and confirm password do not match.";
  } elseif ($mode === 'HARDENED' && strlen($password) < 8) {
    $err = "HARDENED: password must be at least 8 characters.";
  } else {
    $conn = db();

    // Duplicate username check
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();

    if ($exists) {
      $err = "Username already exists. Choose another.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (username, role, password_hash) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $role, $hash);

      if (!$stmt->execute()) {
        $err = "Registration failed. Try again.";
      } else {
        // Auto-login after registration
        if (login_user($username, $password)) {
          header("Location: /wss/attack_surface_lab/ui/dashboard.php");
          exit;
        }
        $msg = "Registration complete. Please login.";
      }
    }
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title>
<style>
  body{font-family:Arial,sans-serif;margin:24px}
  .card{border:1px solid #ddd;border-radius:10px;padding:16px;max-width:640px}
  label{display:block;margin:10px 0 6px}
  input,select{padding:8px;width:100%;max-width:420px}
  .pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#f2f2f2}
</style>
</head><body>
<div class="card">
  <h2>Register</h2>
  <p><span class="pill">Mode: <b><?= htmlspecialchars($mode) ?></b></span> |
     <a href="../mode.php">Switch mode</a> |
     <a href="../index.php">Home</a> |
     <a href="login.php">Login</a>
  </p>

  <?php if ($err): ?><p style="color:#b00020"><b><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></b></p><?php endif; ?>
  <?php if ($msg): ?><p style="color:#0b6"><b><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></b></p><?php endif; ?>

  <form method="POST" autocomplete="off">
    <label>Username</label>
    <input name="username" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <label>Password</label>
    <input name="password" type="password" required>

    <label>Confirm Password</label>
    <input name="confirm" type="password" required>

    <?php if ($mode === 'VULNERABLE'): ?>
      <label>Role (teaching-only; client-controlled)</label>
      <select name="role">
        <option value="student" <?= (($_POST['role'] ?? '')==='student')?'selected':'' ?>>student</option>
        <option value="staff"   <?= (($_POST['role'] ?? '')==='staff')?'selected':'' ?>>staff</option>
        <option value="admin"   <?= (($_POST['role'] ?? '')==='admin')?'selected':'' ?>>admin</option>
      </select>
      <p style="color:#555;margin-top:6px">
        Teaching note: VULNERABLE mode allows role selection to illustrate server-side field allowlisting.
      </p>
    <?php else: ?>
      <p style="color:#555;margin-top:6px">
        HARDENED: role is enforced as <b>student</b> server-side, and password length policy is applied.
      </p>
    <?php endif; ?>

    <br><br>
    <button style="padding:10px 14px">Create Account</button>
  </form>
</div>
</body></html>
