<?php
require_once __DIR__ . '/config.php';
session_start();

if (SECURE_MODE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    // Note: 'Secure' cookies require HTTPS; local demo typically runs on HTTP.
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>McCumber Demo - Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <h2>Student Marks Portal</h2>
    <p class="small">
      Demo focus: McCumber Cube (CIA × Data States × People/Policy/Technology).<br>
      Current mode: <b><?php echo SECURE_MODE ? "SECURE" : "INSECURE"; ?></b>
    </p>

    <?php if (!empty($_GET['err'])): ?>
      <p class="err"><?php echo htmlspecialchars($_GET['err']); ?></p>
    <?php endif; ?>

    <form method="post" action="process_login.php" autocomplete="off">
      <label>Username</label>
      <input name="username" required>

      <label>Password</label>
      <input name="password" type="password" required>

      <button type="submit">Login</button>
    </form>

    <hr>
    <p class="small">
      Test users: <code>alice / 12345</code>, <code>bob / Bob@2026!</code><br>
      Try multiple wrong passwords to observe lockout behavior.
    </p>

    <p class="small">
      <a href="policy.php">View Policy Page</a>
    </p>
  </div>
</body>
</html>
