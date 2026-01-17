<?php
require_once __DIR__ . '/config.php';

function send_json($code, $payload = null, $headers = []) {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  header('X-Lab-App: php-crud-status-lab');
  header('X-Content-Type-Options: nosniff');

  foreach ($headers as $k => $v) header($k . ': ' . $v);

  // For teaching: allow "no body" responses (e.g., 204, 404 in search teaching mode)
  if ($payload === null) {
    header('Content-Length: 0');
    exit;
  }

  echo json_encode($payload, JSON_PRETTY_PRINT);
  exit;
}

function read_json_file($path, $default) {
  if (!file_exists($path)) return $default;
  $raw = @file_get_contents($path);
  if ($raw === false) return $default;
  $data = json_decode($raw, true);
  return is_array($data) ? $data : $default;
}

function write_json_file_atomic($path, $data) {
  // Windows/XAMPP sometimes blocks rename; use two-step with fallback.
  $json = json_encode($data, JSON_PRETTY_PRINT);
  if ($json === false) return false;

  $tmp = $path . '.tmp';
  $ok = @file_put_contents($tmp, $json, LOCK_EX);
  if ($ok === false) return false;

  // Try atomic rename; if fails (common on Windows), write directly.
  if (@rename($tmp, $path)) return true;

  $ok2 = @file_put_contents($path, $json, LOCK_EX);
  @unlink($tmp);
  return $ok2 !== false;
}

function clamp_int($v, $min, $max, $default) {
  if ($v === null || $v === '') return $default;
  if (!is_numeric($v)) return $default;
  $i = intval($v);
  if ($i < $min) return $default;
  if ($i > $max) return $default;
  return $i;
}

function get_client_ip() {
  return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function rate_limit_or_429() {
  $meta = read_json_file(META_FILE, ["next_id" => 1, "rate" => []]);
  $ip = get_client_ip();
  $now = time();
  $window = 60;

  if (!isset($meta["rate"][$ip])) $meta["rate"][$ip] = [];
  $meta["rate"][$ip] = array_values(array_filter($meta["rate"][$ip], function($ts) use ($now, $window) {
    return ($now - $ts) < $window;
  }));

  if (count($meta["rate"][$ip]) >= RATE_LIMIT_PER_MIN) {
    write_json_file_atomic(META_FILE, $meta);
    send_json(429, [
      "ok" => false, "code" => 429,
      "error" => "RATE_LIMITED",
      "message" => "Too many requests. Try again later."
    ], ["Retry-After" => "60"]);
  }

  $meta["rate"][$ip][] = $now;
  write_json_file_atomic(META_FILE, $meta);
}

function require_api_key_or_401_403() {
  $key = $_SERVER['HTTP_X_API_KEY'] ?? '';
  if ($key === '') {
    send_json(401, [
      "ok" => false, "code" => 401,
      "error" => "UNAUTHORIZED",
      "message" => "Missing X-API-Key header."
    ], ["WWW-Authenticate" => 'ApiKey realm="Lab"']);
  }
  if ($key !== API_KEY) {
    send_json(403, [
      "ok" => false, "code" => 403,
      "error" => "FORBIDDEN",
      "message" => "Invalid API key."
    ]);
  }
}

function parse_json_body_or_errors() {
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($ct, 'application/json') === false) {
    send_json(415, [
      "ok" => false, "code" => 415,
      "error" => "UNSUPPORTED_MEDIA_TYPE",
      "message" => "Use Content-Type: application/json"
    ]);
  }
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  if ($data === null && trim($raw) !== '') {
    send_json(400, [
      "ok" => false, "code" => 400,
      "error" => "BAD_JSON",
      "message" => "Malformed JSON body."
    ]);
  }
  if (!is_array($data)) $data = [];
  return $data;
}

function validate_person_or_422($p, $partial=false) {
  $errors = [];

  if (!$partial || array_key_exists("name", $p)) {
    $name = trim(strval($p["name"] ?? ""));
    if ($name === "") $errors["name"] = "Name is required.";
    if (strlen($name) > 60) $errors["name"] = "Name too long (max 60).";
  }

  if (!$partial || array_key_exists("phone", $p)) {
    $phone = preg_replace('/\s+/', '', strval($p["phone"] ?? ""));
    if ($phone === "") $errors["phone"] = "Phone is required.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors["phone"] = "Phone must be exactly 10 digits.";
  }

  if (!$partial || array_key_exists("friends", $p)) {
    $friends = $p["friends"] ?? [];
    if (!is_array($friends)) {
      $errors["friends"] = "Friends must be an array of names.";
    } else {
      foreach ($friends as $i => $f) {
        $s = trim(strval($f));
        if ($s === "") $errors["friends[$i]"] = "Friend name cannot be empty.";
      }
    }
  }

  if (!empty($errors)) {
    send_json(422, [
      "ok" => false, "code" => 422,
      "error" => "VALIDATION_FAILED",
      "message" => "Fix the highlighted fields.",
      "fields" => $errors
    ]);
  }
}

function find_person_index($people, $id) {
  foreach ($people as $idx => $p) {
    if (intval($p["id"]) === intval($id)) return $idx;
  }
  return -1;
}
?>