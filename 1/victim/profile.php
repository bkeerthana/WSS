<?php
session_start();

if(!isset($_SESSION['user'])){
    echo "Not logged in";
    exit;
}

echo "<h2>Welcome " . $_SESSION['user'] . "</h2>";
echo "<br>Session ID: " . session_id();

//XSS vulnerability (intentional)
echo "<br>Comment: " . $_GET['comment'];
?>

<br><br>
<a href="logout.php">Logout</a>