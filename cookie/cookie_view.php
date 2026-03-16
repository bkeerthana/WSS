<?php
date_default_timezone_set("Asia/Kolkata");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Cookie</title>
</head>
<body>
    <h2>Cookie Demo - HTTP Appears Stateful</h2>

    <?php if (isset($_COOKIE['visit_count'])): ?>
        <p><b>Cookie received from browser:</b> visit_count = <?php echo htmlspecialchars($_COOKIE['visit_count']); ?></p>
        <p>The server now remembers something about this browser.</p>
        <p>This creates the effect of statefulness over stateless HTTP.</p>
    <?php else: ?>
        <p><b>No cookie found.</b></p>
        <p>The server has no memory of previous requests.</p>
    <?php endif; ?>

    <hr>
    <p><a href="cookie_set.php">Set/Update Cookie</a></p>
    <p><a href="cookie_delete.php">Delete Cookie</a></p>
</body>
</html>