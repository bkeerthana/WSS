<?php
require_once __DIR__ . "/config.php";

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    ini_set("session.use_only_cookies", "1");
    ini_set("session.use_strict_mode", "1");
    session_start();
  }
}

function login_user(array $user): void {
  start_session();
  if (!lab_mode()) {
    session_regenerate_id(true);
  }
  $_SESSION["user"] = [
    "id" => (int)($user["id"] ?? 0),
    "username" => (string)($user["username"] ?? "unknown"),
    "role" => (string)($user["role"] ?? "student"),
  ];
}

function current_user(): ?array {
  start_session();
  return $_SESSION["user"] ?? null;
}

function require_login(): void {
  $u = current_user();
  if (!$u) {
    header("Location: /wss/attack_surface_auth_lab/ui/login.php?msg=" . urlencode("Please login"));
    exit;
  }
}

function logout_user(): void {
  start_session();
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
  }
  session_destroy();
}
