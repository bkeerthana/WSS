<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user = $_POST["username"] ?? "";
  $pass = $_POST["password"] ?? "";

  // Demo credentials
  if ($user === "demo" && $pass === "demo123") {
    session_regenerate_id(true);
    $_SESSION["user"] = $user;
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Invalid credentials.";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Login - StampVault</title>
</head>
<body>
<h2>Login</h2>
<?php if ($error) { echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; } ?>
<form method="post" autocomplete="off">
  <label>Username</label><br/>
  <input name="username" value="demo"/><br/><br/>
  <label>Password</label><br/>
  <input name="password" type="password" value="demo123"/><br/><br/>
  <button type="submit">Sign in</button>
</form>

<p><a href="index.php">Back</a></p>
</body>
</html>
