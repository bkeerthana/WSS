<?php
date_default_timezone_set("Asia/Kolkata");

$visits = 1;

if (isset($_COOKIE['visit_count'])) {
    $visits = $_COOKIE['visit_count'] + 1;
}

setcookie("visit_count", $visits, time() + 3600, "/");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Cookie</title>
</head>
<body>
    <h2>Cookie Demo - Setting State</h2>
    <p>A cookie named <b>visit_count</b> is being stored in the browser.</p>
    <p><b>Visit count set to:</b> <?php echo $visits; ?></p>

    <hr>
    <p><a href="cookie_view.php">Go to cookie_view.php</a></p>
    <p><a href="stateless.php">Go to stateless.php</a></p>
</body>
</html>