<?php
setcookie("visit_count", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Cookie</title>
</head>
<body>
    <h2>Cookie Deleted</h2>
    <p>The cookie has been removed from the browser.</p>
    <p>Now the server will not remember previous visits through this cookie.</p>

    <hr>
    <p><a href="cookie_view.php">Go to cookie_view.php</a></p>
    <p><a href="cookie_set.php">Go to cookie_set.php</a></p>
</body>
</html>