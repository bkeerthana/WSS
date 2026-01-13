<?php
header("Content-Type: application/json; charset=UTF-8");

$dataFile = __DIR__ . "/data.json";

if (!file_exists($dataFile)) {
  file_put_contents($dataFile, json_encode([
    ["id" => 1, "name" => "Alice", "role" => "Student"],
    ["id" => 2, "name" => "Bob", "role" => "TA"]
  ], JSON_PRETTY_PRINT));
}

function readData($file) {
  $raw = file_get_contents($file);
  $arr = json_decode($raw, true);
  return is_array($arr) ? $arr : [];
}

function writeData($file, $arr) {
  file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT));
}

function getJsonBody() {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $decoded = json_decode($raw, true);
  return is_array($decoded) ? $decoded : [];
}

function send($payload, $code = 200) {
  http_response_code($code);
  echo json_encode($payload, JSON_PRETTY_PRINT);
  exit;
}

$method = $_SERVER["REQUEST_METHOD"];
$data   = readData($dataFile);
$id     = isset($_GET["id"]) ? intval($_GET["id"]) : null;

switch ($method) {

  case "GET":
    if ($id) {
      foreach ($data as $row) {
        if ($row["id"] === $id) send(["method" => "GET", "user" => $row], 200);
      }
      send(["method" => "GET", "error" => "User not found", "id" => $id], 404);
    }
    send(["method" => "GET", "users" => $data], 200);

  case "POST":
    $body = getJsonBody();
    if (!isset($body["name"]) || !isset($body["role"])) {
      send(["method" => "POST", "error" => "Missing name/role"], 400);
    }
    $maxId = 0;
    foreach ($data as $row) $maxId = max($maxId, $row["id"]);

    $new = [
      "id" => $maxId + 1,
      "name" => strval($body["name"]),
      "role" => strval($body["role"])
    ];

    $data[] = $new;
    writeData($dataFile, $data);
    send(["method" => "POST", "message" => "Created", "created" => $new], 201);

  case "PUT":
    if (!$id) send(["method" => "PUT", "error" => "Missing ?id="], 400);
    $body = getJsonBody();
    if (!isset($body["name"]) || !isset($body["role"])) {
      send(["method" => "PUT", "error" => "PUT requires full body: name + role"], 400);
    }

    $updated = null;
    for ($i = 0; $i < count($data); $i++) {
      if ($data[$i]["id"] === $id) {
        $data[$i]["name"] = strval($body["name"]);
        $data[$i]["role"] = strval($body["role"]);
        $updated = $data[$i];
        break;
      }
    }
    if (!$updated) send(["method" => "PUT", "error" => "User not found", "id" => $id], 404);

    writeData($dataFile, $data);
    send(["method" => "PUT", "message" => "Updated", "updated" => $updated], 200);

  case "DELETE":
    if (!$id) send(["method" => "DELETE", "error" => "Missing ?id="], 400);

    $before = count($data);
    $data = array_values(array_filter($data, function($row) use ($id) {
      return $row["id"] !== $id;
    }));

    if (count($data) === $before) {
      send(["method" => "DELETE", "error" => "User not found", "id" => $id], 404);
    }

    writeData($dataFile, $data);
    send(["method" => "DELETE", "message" => "Deleted", "deleted_id" => $id], 200);

  default:
    send(["error" => "Method not allowed"], 405);
}
?>
