<?php
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../auth.php";

header("Content-Type: text/html; charset=UTF-8");

$err = "";
$msg = "";

if (isset($_GET["err"])) $err = (string)$_GET["err"];
if (isset($_GET["msg"])) $msg = (string)$_GET["msg"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim((string)($_POST["username"] ?? ""));
  $password = (string)($_POST["password"] ?? "");

  if ($username === "" || $password === "") {
    $err = "Username and password required.";
  } else {
    $conn = db();

    // Fetch user record
    $stmt = $conn->prepare("SELECT id, username, role, password_hash FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
      $err = "DB error: prepare failed (SELECT users).";
    } else {
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $res = $stmt->get_result();
      $row = $res ? $res->fetch_assoc() : null;

      if (!$row) {
        $err = "Invalid credentials.";
      } else {
        $hash = (string)($row["password_hash"] ?? "");

        // Verify password (supports hashed passwords)
        if ($hash === "" || !password_verify($password, $hash)) {
          $err = "Invalid credentials.";
        } else {
          // ✅ SUCCESS: Option A — pass user array to login_user()
          login_user([
            "id"       => (int)$row["id"],
            "username" => (string)$row["username"],
            "role"     => (string)$row["role"],
          ]);

          header("Location: /wss/attack_surface_lab/ui/dashboard.php");
          exit;
        }
      }
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .card { max-width: 420px; border: 1px solid #ddd; border-radius: 10px; padding: 16px; }
    input { width: 100%; padding: 10px; margin: 8px 0; }
    button { padding: 10px 14px; }
    .err { color: #b00020; }
    .msg { color: #0b6; }
  </style>
</head>
<body>
  <div class="card">
    <h3>Login</h3>

    <?php if ($msg): ?>
      <p class="msg"><b><?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?></b></p>
    <?php endif; ?>

    <?php if ($err): ?>
      <p class="err"><b><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></b></p>
    <?php endif; ?>

    <form method="POST">
      <label>Username</label>
      <input name="username" autocomplete="username" required>

      <label>Password</label>
      <input name="password" type="password" autocomplete="current-password" required>

      <button type="submit">Login</button>
    </form>

    <p style="margin-top:12px">
      <a href="/wss/attack_surface_lab/ui/register.php">Register</a>
    </p>
  </div>
</body>
</html>
