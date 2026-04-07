<?php
require_once 'common.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['user'] === "student" && $_POST['pass'] === "1234") {
        $_SESSION['user'] = "student";
        $_SESSION['balance'] = 10000;
        generate_csrf_token();
        header("Location: home.php");
        exit;
    }
}
?>

<h2>Bank Login</h2>
<p>Username: student | Password: 1234</p>

<form method="POST">
    Username: <input name="user"><br>
    Password: <input type="password" name="pass"><br><br>
    <button>Login</button>
</form>
