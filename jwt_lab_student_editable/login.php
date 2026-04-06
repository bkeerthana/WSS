<?php
require 'config.php';
require 'jwt_helper.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!isset($USERS[$username]) || $USERS[$username]['password'] !== $password) {
    die("Invalid login. <a href='index.php'>Back</a>");
}

$user = $USERS[$username];

$payload = [
    "sub"   => $username,
    "name"  => $user['name'],
    "role"  => $user['role'],
    "iat"   => time(),
    "exp"   => time() + 300,
    "email" => $username . "@example.com"
];

// ==========================================================
// LAB TASK 4: Change algorithm from "HS256" to "none"
// and observe what happens in secure and insecure pages.
// ==========================================================
$jwt = create_jwt($payload, $JWT_SECRET, "HS256");

// ==========================================================
// LAB TASK 6: Remove or comment out exp above and observe:
// - secure pages reject missing exp
// - copied token behavior changes
// ==========================================================

setcookie("jwt_token", $jwt, time() + 3600, "/");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Success</title>
    <style>body{font-family:Arial;margin:30px;} textarea{width:100%;}</style>
</head>
<body>
    <h2>Login Successful</h2>
    <p>JWT generated and stored in cookie.</p>

    <h3>Generated Token</h3>
    <textarea rows="8"><?php echo htmlspecialchars($jwt); ?></textarea>

    <h3>Try these pages</h3>
    <p><a href="debug_token.php">Debug Token</a></p>
    <p><a href="student_dashboard.php">Student Dashboard</a></p>
    <p><a href="faculty_dashboard.php">Faculty Dashboard</a></p>
    <p><a href="admin_dashboard.php">Admin Dashboard</a></p>
    <p><a href="profile.php">Profile</a></p>
    <p><a href="admin_insecure.php">Admin Insecure Page</a></p>
    <p><a href="student_open.php">Student Open Page (No Auth Check)</a></p>
    <p><a href="tamper_token.php">Tamper Token</a></p>
    <p><a href="forge_token.php">Forge Token</a></p>
    <p><a href="manual_reuse.php">Manual Token Reuse</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
