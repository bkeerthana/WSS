<?php
require 'auth_secure.php';
$payload = require_authentication_and_role(['faculty']);
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Faculty Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($payload['name']); ?>.</p>
<p>This secure page is intended for <strong>faculty</strong> only.</p>
<p><a href="index.php">Home</a> | <a href="logout.php">Logout</a></p>
</body></html>
