<?php
// Receiver: accepts JSON and stores messages into messages.jsonl (one JSON object per line)
header("Content-Type: application/json; charset=UTF-8");

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data) || !isset($data["message"])) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Invalid JSON or missing 'message'"]);
  exit;
}

$record = [
  "received_at" => date("c"),
  "remote_addr" => $_SERVER["REMOTE_ADDR"] ?? "",
  "user_agent"  => $_SERVER["HTTP_USER_AGENT"] ?? "",
  "message"     => (string)$data["message"],
  "session_id_hash" => $data["session_id_hash"] ?? null,
  "demo_token"  => $data["demo_token"] ?? null,
  "ts"          => $data["ts"] ?? null,
  "page"        => $data["page"] ?? null,
  "cookies_visible_to_js" => $data["cookies_visible_to_js"] ?? null
];

$file = __DIR__ . "/messages.jsonl";
file_put_contents($file, json_encode($record) . PHP_EOL, FILE_APPEND);

echo json_encode(["ok" => true, "stored" => "messages.jsonl"]);
