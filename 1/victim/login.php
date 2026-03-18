<?php
session_start();
session_regenerate_id(true);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['user'] = $_POST['username'];

    //vulnerable (no regeneration)
    echo "Login successful <a href='profile.php'>Go to Profile</a>";
}
?>

<h2>Login</h2>
<form method="POST">
    Username: <input type="text" name="username">
    <button type="submit">Login</button>
</form>