<?php
require 'jwt_helper.php';

$current_token = $_COOKIE['jwt_token'] ?? '';
$modified_token = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['source_token'])) {
    $source_token = $_POST['source_token'];
    $parts = get_jwt_parts($source_token);

    if ($parts) {
        $payload = json_decode(base64url_decode($parts[1]), true);
        if (is_array($payload)) {
            $payload['role'] = $_POST['new_role'] ?? 'admin';
            $new_payload_encoded = base64url_encode(json_encode($payload));

            // signature intentionally not recalculated
            $modified_token = $parts[0] . "." . $new_payload_encoded . "." . $parts[2];
        }
    }
}
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Payload Tampering</h2>
<p>Change the role in payload without recalculating the signature.</p>

<form method="post">
    <label>Source Token</label><br>
    <textarea name="source_token" rows="8" style="width:100%;"><?php echo htmlspecialchars($current_token); ?></textarea><br><br>

    <label>New Role</label><br>
    <select name="new_role">
        <option value="admin">admin</option>
        <option value="faculty">faculty</option>
        <option value="student">student</option>
    </select><br><br>

    <button type="submit">Tamper Token</button>
</form>

<?php if ($modified_token): ?>
<h3>Tampered Token</h3>
<textarea rows="8" style="width:100%;"><?php echo htmlspecialchars($modified_token); ?></textarea>
<p>Paste this in manual_reuse.php or jwt.io and observe signature failure.</p>
<?php endif; ?>

<p><a href="index.php">Home</a></p>
</body></html>
