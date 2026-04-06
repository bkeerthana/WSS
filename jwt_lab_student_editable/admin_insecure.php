<?php
require 'jwt_helper.php';

$jwt = $_COOKIE['jwt_token'] ?? '';
$payload = get_jwt_payload($jwt);

// ==========================================================
// LAB TASK 3 alternative:
// This page is intentionally insecure because it trusts the
// decoded payload directly instead of verifying the signature.
// ==========================================================
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Admin Insecure Page</h2>
<p>This page trusts the payload directly.</p>

<?php if (($payload['role'] ?? '') === 'admin'): ?>
    <p><strong>INSECURE ADMIN ACCESS GRANTED</strong></p>
<?php else: ?>
    <p>Access denied because payload role is not admin.</p>
<?php endif; ?>

<p><a href="index.php">Home</a></p>
</body></html>
