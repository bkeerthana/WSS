<?php
require 'auth_secure.php';

// ==========================================================
// LAB TASK 2:
// Change ['student'] to ['student','faculty','admin']
// and observe broken authorization.
// ==========================================================
$payload = require_authentication_and_role(['student']);
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Student Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($payload['name']); ?>.</p>
<p>This secure page is intended for <strong>student</strong> only.</p>
<p><a href="index.php">Home</a> | <a href="logout.php">Logout</a></p>
</body></html>
