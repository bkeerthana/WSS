<?php
require 'config.php';
require 'jwt_helper.php';

$forged_token = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? 'attacker';
    $role = $_POST['role'] ?? 'admin';

    $payload = [
        "sub" => $username,
        "name" => ucfirst($username),
        "role" => $role,
        "iat" => time(),
        "exp" => time() + 3600,
        "email" => $username . "@example.com"
    ];

    $forged_token = create_jwt($payload, $JWT_SECRET, "HS256");
}
?>
<!DOCTYPE html>
<html><body style="font-family:Arial;margin:30px;">
<h2>Weak Secret Key Demo</h2>
<p>After students change the secret in config.php to a weak value such as <code>12345</code>, explain why forged tokens become dangerous.</p>

<form method="post">
    <label>Username</label><br>
    <input type="text" name="username" value="attacker"><br><br>

    <label>Role</label><br>
    <select name="role">
        <option value="admin">admin</option>
        <option value="faculty">faculty</option>
        <option value="student">student</option>
    </select><br><br>

    <button type="submit">Forge Token</button>
</form>

<?php if ($forged_token): ?>
<h3>Forged Token</h3>
<textarea rows="8" style="width:100%;"><?php echo htmlspecialchars($forged_token); ?></textarea>
<?php endif; ?>

<p><a href="index.php">Home</a></p>
</body></html>
