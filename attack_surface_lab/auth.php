<?php
// auth.php — session management + session fixation demo toggle
// Requires: config.php with lab_mode() and (optionally) MODE_FILE constant.

require_once __DIR__ . "/config.php";

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    // Good defaults for demo; adjust if your app already sets these elsewhere.
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    // If you run on HTTPS later, set cookie_secure=1.
    session_set_cookie_params([
      'httponly' => true,
      'samesite' => 'Lax',
      'secure'   => false,
    ]);
    session_start();
  }
}

/**
 * Returns the current authenticated user from session, or null.
 * Stored as array: ['id'=>..., 'username'=>..., 'role'=>...]
 */
function current_user(): ?array {
  start_session();
  return isset($_SESSION['user']) && is_array($_SESSION['user'])
    ? $_SESSION['user']
    : null;
}

function require_login(): void {
  $u = current_user();
  if (!$u) {
    header("Location: /wss/attack_surface_lab/ui/login.php?err=login_required");
    exit;
  }
}

function require_role(array $roles): void {
  $u = current_user();
  if (!$u) {
    header("Location: /wss/attack_surface_lab/ui/login.php?err=login_required");
    exit;
  }
  $r = strtolower((string)($u['role'] ?? ''));
  $roles = array_map(fn($x) => strtolower((string)$x), $roles);
  if (!in_array($r, $roles, true)) {
    http_response_code(403);
    echo "403 Forbidden (role required)";
    exit;
  }
}

/**
 * SESSION FIXATION CONTROL POINT
 *
 * Call this ONLY after credentials are verified.
 * - VULNERABLE: does NOT regenerate session id (fixation possible)
 * - HARDENED: regenerates session id (fixation prevented)
 */
function login_user(array $user): void {
  start_session();

  // Optional: store "pre-login" session id for teaching/verification
  $_SESSION['debug_pre_login_sid'] = session_id();

  if (lab_mode() === "HARDENED") {
    // Fix: prevent session fixation
    session_regenerate_id(true);
  } else {
    // VULNERABLE: no regeneration => fixation possible
    // (intentionally empty)
  }

  // Optional: store "post-login" session id for teaching
  $_SESSION['debug_post_login_sid'] = session_id();

  // Store only minimal identity info in session
  $_SESSION['user'] = [
    'id'       => (int)($user['id'] ?? 0),
    'username' => (string)($user['username'] ?? ''),
    'role'     => (string)($user['role'] ?? 'student'),
  ];
}

/**
 * Proper logout: destroys session and cookie.
 * (You can still keep your "missing logout protection" demo separately
 * by bypassing this in VULNERABLE mode at ui/logout.php.)
 */
function logout_user(): void {
  start_session();

  // Unset all session vars
  $_SESSION = [];

  // Delete session cookie
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }

  session_destroy();
}

/**
 * Helper for teaching: show session id info safely.
 */
function session_debug_info(): array {
  start_session();
  return [
    'mode' => lab_mode(),
    'current_sid' => session_id(),
    'pre_login_sid' => $_SESSION['debug_pre_login_sid'] ?? null,
    'post_login_sid' => $_SESSION['debug_post_login_sid'] ?? null,
  ];
}
