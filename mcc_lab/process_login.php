<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

session_start();

function nowUtc(): DateTime {
    return new DateTime("now", new DateTimeZone("UTC"));
}

function getAttemptRow(PDO $pdo, string $username): array {
    $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) {
        $ins = $pdo->prepare("INSERT INTO login_attempts (username, attempts, locked_until) VALUES (?, 0, NULL)");
        $ins->execute([$username]);
        return ['username' => $username, 'attempts' => 0, 'locked_until' => null];
    }
    return $row;
}

function updateAttempt(PDO $pdo, string $username, int $attempts, ?string $locked_until): void {
    $stmt = $pdo->prepare("UPDATE login_attempts SET attempts=?, locked_until=? WHERE username=?");
    $stmt->execute([$attempts, $locked_until, $username]);
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.php?err=Missing+credentials");
    exit;
}

// Technology control: naive rate limiting (adds latency per attempt)
if (TECH_RATE_LIMIT_MS > 0) {
    usleep(TECH_RATE_LIMIT_MS * 1000);
}

$pdo = db();
$attempt = getAttemptRow($pdo, $username);

// Lockout check (Policy enforcement)
if (!empty($attempt['locked_until'])) {
    $lockedUntil = new DateTime($attempt['locked_until'], new DateTimeZone("UTC"));
    if ($lockedUntil > nowUtc()) {
        header("Location: login.php?err=Account+locked.+Try+later.");
        exit;
    }
}

// Fetch user
if (SECURE_MODE) {
    // Technology: prepared statements
    $stmt = $pdo->prepare("SELECT id, username, password_value, marks FROM users WHERE username = ?");
    $stmt->execute([$username]);
} else {
    // INSECURE: vulnerable to SQL Injection in username field
    $sql = "SELECT id, username, password_value, marks FROM users WHERE username = '$username'";
    $stmt = $pdo->query($sql);
}

$user = $stmt->fetch();

$valid = false;
if ($user) {
    if (SECURE_MODE) {
        $valid = password_verify($password, $user['password_value']);
    } else {
        // INSECURE: plain-text comparison
        $valid = ($password === $user['password_value']);
    }
}

if (!$valid) {
    $attempts = (int)$attempt['attempts'] + 1;

    if ($attempts >= POLICY_MAX_FAILED_ATTEMPTS) {
        $lockedUntil = nowUtc();
        $lockedUntil->modify("+" . POLICY_LOCKOUT_SECONDS . " seconds");
        updateAttempt($pdo, $username, $attempts, $lockedUntil->format("Y-m-d H:i:s"));
        header("Location: login.php?err=Too+many+attempts.+Locked+for+" . POLICY_LOCKOUT_SECONDS . "+seconds");
        exit;
    }

    updateAttempt($pdo, $username, $attempts, null);
    header("Location: login.php?err=Invalid+credentials+(attempt+$attempts)");
    exit;
}

// Successful login: reset attempts
updateAttempt($pdo, $username, 0, null);

// Session hardening (Technology)
if (SECURE_MODE) {
    session_regenerate_id(true);
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

header("Location: dashboard.php");
exit;
?>
