<?php
setcookie("jwt_token", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Logged Out</h2>
<p>The browser cookie has been removed.</p>
<p>But if the token was copied earlier, it can still be tested in manual_reuse.php until it expires.</p>
<p><a href="manual_reuse.php">Test Copied Token</a></p>
<p><a href="index.php">Home</a></p>
</body></html>
