<?php
require_once __DIR__ . "/../db.php";
header("Content-Type: text/html; charset=UTF-8");

$err = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim((string)($_POST["username"] ?? ""));
  $password = (string)($_POST["password"] ?? "");

  if ($username === "" || $password === "") {
    $err = "Username and password required.";
  } else {
    $conn = db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = "student";

    $stmt = $conn->prepare("INSERT INTO users (username, role, password_hash) VALUES (?, ?, ?)");
    if (!$stmt) $err = "DB error: prepare failed.";
    else {
      $stmt->bind_param("sss", $username, $role, $hash);
      if ($stmt->execute()) {
        header("Location: /wss/attack_surface_auth_lab/ui/login.php?msg=" . urlencode("Registered. Please login."));
        exit;
      } else $err = "Registration failed (username may already exist).";
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <style>
    body{font-family:Arial,sans-serif;padding:20px}
    .card{max-width:520px;border:1px solid #ddd;border-radius:10px;padding:16px}
    input{width:100%;padding:10px;margin:8px 0}
    button{padding:10px 14px}
    .err{color:#b00020}
  </style>
</head>
<body>
<div class="card">
  <h3>Register</h3>
  <?php if ($err): ?><p class="err"><b><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></b></p><?php endif; ?>
  <form method="POST">
    <label>Username</label>
    <input name="username" required>
    <label>Password</label>
    <input name="password" type="password" required>
    <button type="submit">Create account</button>
  </form>
  <p style="margin-top:12px"><a href="/wss/attack_surface_auth_lab/ui/login.php">Back to Login</a></p>
</div>
</body>
</html>
