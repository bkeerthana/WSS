<?php
session_start();
$_SESSION['user'] = "Keerthana";
$_SESSION['role'] = "student";
echo "<h2>Login Successful</h2>";
echo "<p>User: " . $_SESSION['user'] . "</p>";
echo "<p><a href='profile.php'>Go to Profile</a></p>";
?>