<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../db.php";
header("Content-Type: application/json; charset=utf-8");

$mode = lab_mode();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$allowed = ['GET', 'POST'];

if ($method === 'OPTIONS') {
  header('Allow: ' . implode(', ', $allowed));
  header('Access-Control-Allow-Methods: ' . implode(', ', $allowed));
  http_response_code(204);
  exit;
}

$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if ($apiKey !== LAB_API_KEY) {
  http_response_code(401);
  echo json_encode(["ok"=>false,"error"=>"UNAUTHORIZED","message"=>"Missing/invalid X-API-Key"], JSON_PRETTY_PRINT);
  exit;
}

if (!in_array($method, $allowed, true)) {
  http_response_code(405);
  header('Allow: ' . implode(', ', $allowed));
  echo json_encode(["ok"=>false,"error"=>"METHOD_NOT_ALLOWED","allowed"=>$allowed], JSON_PRETTY_PRINT);
  exit;
}

$conn = db();

if ($method === 'GET') {
  $id = $_GET['id'] ?? null;
  if ($id !== null && $id !== '') {
    $idInt = (int)$id;

    if ($mode === 'HARDENED') {
      $role = strtolower($_SERVER['HTTP_X_ROLE'] ?? '');
      if (!in_array($role, ['admin','staff'], true)) {
        http_response_code(403);
        echo json_encode(["ok"=>false,"error"=>"FORBIDDEN","message"=>"HARDENED: querying specific user requires X-ROLE=admin|staff"], JSON_PRETTY_PRINT);
        exit;
      }
    }

    # $stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
    if ($mode === 'VULNERABLE') {
  // VULNERABLE: excessive exposure (teaching demo)
  $stmt = $conn->prepare("SELECT id, username, role, password_hash, created_at FROM users WHERE id = ?");
} else {
  // HARDENED: least-privilege response
  $stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
}

    $stmt->bind_param("i", $idInt);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    echo json_encode(["ok"=>true,"mode"=>$mode,"user"=>$row], JSON_PRETTY_PRINT);
    exit;
  }

 # $res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
 if ($mode === 'VULNERABLE') {
  $res = $conn->query("SELECT id, username, role, password_hash, created_at FROM users ORDER BY id ASC");
} else {
  $res = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
}

  $rows = [];
  while ($r = $res->fetch_assoc()) $rows[] = $r;
  echo json_encode(["ok"=>true,"mode"=>$mode,"count"=>count($rows),"users"=>$rows], JSON_PRETTY_PRINT);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data) || !isset($data['username'])) {
  http_response_code(400);
  echo json_encode(["ok"=>false,"error"=>"BAD_JSON","message"=>"Expected JSON with 'username'"], JSON_PRETTY_PRINT);
  exit;
}

$username = (string)$data['username'];
$role = ($mode === 'VULNERABLE') ? (string)($data['role'] ?? 'student') : 'student';

$randomPass = bin2hex(random_bytes(8));
$passHash = password_hash($randomPass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, role, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $role, $passHash);

if (!$stmt->execute()) {
  http_response_code(409);
  echo json_encode(["ok"=>false,"error"=>"CONFLICT","message"=>"Username may already exist"], JSON_PRETTY_PRINT);
  exit;
}

echo json_encode(["ok"=>true,"mode"=>$mode,"message"=>"User created (API demo)","effective_role"=>$role], JSON_PRETTY_PRINT);
