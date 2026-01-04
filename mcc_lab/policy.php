<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>McCumber Demo - Policy</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <h2>Security Policy (Demo)</h2>
    <p>This page represents the <b>Policy</b> dimension of McCumber.</p>
    <ul>
      <li>Minimum password length: <b><?php echo POLICY_MIN_PASSWORD_LEN; ?></b></li>
      <li>Max failed attempts: <b><?php echo POLICY_MAX_FAILED_ATTEMPTS; ?></b></li>
      <li>Lockout duration: <b><?php echo POLICY_LOCKOUT_SECONDS; ?></b> seconds</li>
    </ul>
    <p class="small">
      People dimension: users must choose non-trivial passwords, do not share credentials, log out after use.
    </p>
    <p><a href="login.php">Back to Login</a></p>
  </div>
</body>
</html>
