<?php
require 'auth_secure.php';
$payload = require_authentication_and_role(['student','faculty','admin']);
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Profile Page</h2>
<pre><?php print_r($payload); ?></pre>
<p><a href="index.php">Home</a> | <a href="logout.php">Logout</a></p>
</body></html>
