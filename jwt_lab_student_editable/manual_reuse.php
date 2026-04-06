<?php
require 'config.php';
require 'jwt_helper.php';

$message = '';
$token = $_POST['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_jwt_signature($token, $JWT_SECRET)) {
        $message = "Rejected: invalid signature.";
    } else {
        $payload = get_jwt_payload($token);
        if (!$payload) {
            $message = "Rejected: invalid payload.";
        } elseif (!isset($payload['exp'])) {
            $message = "Signature valid, but exp claim is missing.";
        } elseif ($payload['exp'] < time()) {
            $message = "Rejected: token expired.";
        } else {
            $message = "Copied token still works. User=" . ($payload['sub'] ?? 'unknown') . ", Role=" . ($payload['role'] ?? 'unknown');
        }
    }
}
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Manual Token Reuse</h2>
<p>Paste a copied token here. Use this after logout to show post-logout token reuse.</p>

<form method="post">
    <textarea name="token" rows="8" style="width:100%;"><?php echo htmlspecialchars($token); ?></textarea><br><br>
    <button type="submit">Test Token</button>
</form>

<?php if ($message): ?>
<p><strong><?php echo htmlspecialchars($message); ?></strong></p>
<?php endif; ?>

<p><a href="index.php">Home</a></p>
</body></html>
