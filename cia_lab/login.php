<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

session_name(SESSION_NAME);
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    // CIA (Confidentiality): never store raw passwords; verify against hash
    $stmt = db()->prepare("SELECT id, username, password_hash, full_name, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // CIA (Integrity): session stores server-trusted identity/role (not from client)
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
        ];

        // Regenerate session ID after login (good practice; helps confidentiality)
        session_regenerate_id(true);

        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - <?= APP_NAME ?></title>
</head>
<body>
  <h2>Login</h2>

  <?php if ($error): ?>
    <p style="color:red;"><b><?= htmlspecialchars($error) ?></b></p>
  <?php endif; ?>

  <form method="post" action="login.php" autocomplete="off">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
  </form>

  <p><a href="index.php">Home</a></p>

  <hr>
  <p><b>Demo accounts (after you run seed_users.php):</b></p>
  <ul>
    <li>alice / Alice@123 (user)</li>
    <li>bob / Bob@123 (user)</li>
    <li>admin / Admin@123 (admin)</li>
  </ul>
</body>
</html>
