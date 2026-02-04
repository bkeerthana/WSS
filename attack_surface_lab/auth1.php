<?php
require_once __DIR__ . "/db.php";

/**
 * Start session safely
 */
function start_session(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
}

/**
 * Get current logged-in user
 * Returns:
 *  - array  → valid logged-in user
 *  - null   → not logged in / invalid session
 */
function current_user(): ?array {
  start_session();

  if (!isset($_SESSION['user'])) {
    return null;
  }

  $u = $_SESSION['user'];

  // 🔒 Backward compatibility:
  // If old code stored username as string, invalidate session
  if (!is_array($u)) {
    unset($_SESSION['user']);
    return null;
  }

  // 🔒 Enforce expected structure
  if (!isset($u['id'], $u['username'], $u['role'])) {
    unset($_SESSION['user']);
    return null;
  }

  return $u;
}

/**
 * Require login for protected pages
 */
function require_login(): void {
  if (!current_user()) {
    header("Location: /wss/attack_surface_lab/ui/login.php");
    exit;
  }
}

/**
 * Require specific role(s)
 */
function require_role(array $roles): void {
  $u = current_user();
  if (!$u) {
    header("Location: /wss/attack_surface_lab/ui/login.php");
    exit;
  }

  $role = strtolower($u['role']);
  $roles = array_map('strtolower', $roles);

  if (!in_array($role, $roles, true)) {
    http_response_code(403);
    header("Content-Type: text/html; charset=UTF-8");
    echo "<h3>403 Forbidden</h3>";
    echo "<p>Insufficient privileges.</p>";
    echo "<p><a href='/wss/attack_surface_lab/ui/dashboard.php'>Back</a></p>";
    exit;
  }
}

/**
 * Login user using username + password
 */
function login_user(string $username, string $password): bool {
  require_once __DIR__ . "/config.php";  // to use lab_mode()

  $conn = db();

  $stmt = $conn->prepare("SELECT id, username, role, password_hash FROM users WHERE username = ?");
  if (!$stmt) return false;

  $stmt->bind_param("s", $username);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  if (!$row) return false;

  $mode = lab_mode();

  if ($mode === "VULNERABLE") {
    /**
     * VULNERABLE: Weak authentication demo
     * - Accepts any password for an existing username.
     * Teaching point: "User existence check is not authentication."
     */
    // No password verification here (intentional for demo)
  } else {
    /**
     * HARDENED: Proper authentication
     */
    $hash = (string)($row['password_hash'] ?? '');
    if ($hash === '' || !password_verify($password, $hash)) {
      return false;
    }
  }

  start_session();
  session_regenerate_id(true);

  $_SESSION['user'] = [
    "id"       => (int)$row['id'],
    "username" => (string)$row['username'],
    "role"     => (string)$row['role'],
  ];

  return true;
}


/**
 * Logout user cleanly
 */
function logout_user(): void {
  start_session();
  $_SESSION = [];

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params["path"],
      $params["domain"],
      $params["secure"],
      $params["httponly"]
    );
  }

  session_destroy();
}
