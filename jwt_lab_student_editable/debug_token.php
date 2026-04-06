<?php
require 'jwt_helper.php';
$jwt = $_COOKIE['jwt_token'] ?? '';
$header = $jwt ? get_jwt_header($jwt) : null;
$payload = $jwt ? get_jwt_payload($jwt) : null;
$parts = $jwt ? get_jwt_parts($jwt) : null;
$signature = $parts ? $parts[2] : '';
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Debug Token</h2>

<?php if (!$jwt): ?>
<p>No token found in cookie.</p>
<?php else: ?>
<h3>Full Token</h3>
<textarea rows="8" style="width:100%;"><?php echo htmlspecialchars($jwt); ?></textarea>

<h3>Header</h3>
<pre><?php print_r($header); ?></pre>

<h3>Payload</h3>
<pre><?php print_r($payload); ?></pre>

<h3>Signature</h3>
<pre><?php echo htmlspecialchars($signature); ?></pre>
<?php endif; ?>

<p><a href="index.php">Home</a></p>
</body></html>
