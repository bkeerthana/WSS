<?php
require_once __DIR__ . '/../src/lib.php';

rate_limit_or_429();
require_api_key_or_401_403();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

$allowed = ["GET","POST","PUT","PATCH","DELETE","OPTIONS"];
if (!in_array($method, $allowed)) {
  send_json(405, null, ["Allow" => implode(", ", $allowed)]);
}

if ($method === "OPTIONS") {
  send_json(200, ["ok"=>true,"code"=>200,"message"=>"OPTIONS OK"], ["Allow" => implode(", ", $allowed)]);
}

$people = read_json_file(DATA_FILE, []);
$meta = read_json_file(META_FILE, ["next_id" => 1, "rate" => []]);

try {
  if ($method === "GET") {
    if ($action === "list" || $action === "") {
      send_json(200, ["ok"=>true,"code"=>200,"data"=>$people,"count"=>count($people)]);
    }
    if ($action === "get") {
      $id = intval($_GET["id"] ?? 0);
      if ($id <= 0) send_json(400, ["ok"=>false,"code"=>400,"error"=>"BAD_REQUEST","message"=>"Provide a valid id."]);
      $idx = find_person_index($people, $id);
      if ($idx < 0) send_json(404, ["ok"=>false,"code"=>404,"error"=>"NOT_FOUND","message"=>"Person not found."]);
      send_json(200, ["ok"=>true,"code"=>200,"data"=>$people[$idx]]);
    }
    if ($action === "search") {
      $q = trim(strval($_GET["q"] ?? ""));
      if ($q === "") send_json(400, ["ok"=>false,"code"=>400,"error"=>"BAD_REQUEST","message"=>"Provide q for search."]);
      $qL = mb_strtolower($q);
      $matches = array_values(array_filter($people, function($p) use ($qL) {
        $name = mb_strtolower(strval($p["name"] ?? ""));
        $phone = strval($p["phone"] ?? "");
        $friends = $p["friends"] ?? [];
        $friendsStr = mb_strtolower(implode(" ", is_array($friends) ? $friends : []));
        return (strpos($name, $qL) !== false) || (strpos($phone, $qL) !== false) || (strpos($friendsStr, $qL) !== false);
      }));
      if (count($matches) === 0) {
        // Teaching mode requested: no match => 404 and no body.
        send_json(404, null);
      }
      send_json(200, ["ok"=>true,"code"=>200,"q"=>$q,"matches"=>$matches,"count"=>count($matches)]);
    }
    send_json(404, ["ok"=>false,"code"=>404,"error"=>"NOT_FOUND","message"=>"Unknown action for GET."]);
  }

  if ($method === "POST") {
    $body = parse_json_body_or_errors();
    validate_person_or_422($body, false);

    foreach ($people as $p) {
      if (strval($p["phone"]) === strval($body["phone"])) {
        send_json(409, ["ok"=>false,"code"=>409,"error"=>"CONFLICT","message"=>"Phone number already exists."]);
      }
    }

    $id = intval($meta["next_id"] ?? 1);
    $meta["next_id"] = $id + 1;

    $new = [
      "id" => $id,
      "name" => trim(strval($body["name"])),
      "phone" => preg_replace('/\s+/', '', strval($body["phone"])),
      "friends" => array_values($body["friends"] ?? []),
      "created_at" => gmdate('c')
    ];
    $people[] = $new;

    if (!write_json_file_atomic(DATA_FILE, $people) || !write_json_file_atomic(META_FILE, $meta)) {
      send_json(500, ["ok"=>false,"code"=>500,"error"=>"SERVER_ERROR","message"=>"Failed to write storage."]);
    }
    send_json(201, ["ok"=>true,"code"=>201,"data"=>$new], ["Location" => "/php-crud-status-lab/public/api.php?action=get&id=".$id]);
  }

  if ($method === "PUT" || $method === "PATCH") {
    $id = intval($_GET["id"] ?? 0);
    if ($id <= 0) send_json(400, ["ok"=>false,"code"=>400,"error"=>"BAD_REQUEST","message"=>"Provide a valid id."]);
    $idx = find_person_index($people, $id);
    if ($idx < 0) send_json(404, ["ok"=>false,"code"=>404,"error"=>"NOT_FOUND","message"=>"Person not found."]);

    $body = parse_json_body_or_errors();
    $partial = ($method === "PATCH");
    validate_person_or_422($body, $partial);

    if ($method === "PUT") {
      foreach (["name","phone","friends"] as $req) {
        if (!array_key_exists($req, $body)) {
          send_json(422, ["ok"=>false,"code"=>422,"error"=>"VALIDATION_FAILED","message"=>"PUT requires name, phone, friends."]);
        }
      }
    }

    if (array_key_exists("phone", $body)) {
      $newPhone = preg_replace('/\s+/', '', strval($body["phone"]));
      foreach ($people as $p) {
        if (intval($p["id"]) !== $id && strval($p["phone"]) === $newPhone) {
          send_json(409, ["ok"=>false,"code"=>409,"error"=>"CONFLICT","message"=>"Phone already exists for another record."]);
        }
      }
    }

    if (array_key_exists("name", $body)) $people[$idx]["name"] = trim(strval($body["name"]));
    if (array_key_exists("phone", $body)) $people[$idx]["phone"] = preg_replace('/\s+/', '', strval($body["phone"]));
    if (array_key_exists("friends", $body)) $people[$idx]["friends"] = array_values($body["friends"]);
    $people[$idx]["updated_at"] = gmdate('c');

    if (!write_json_file_atomic(DATA_FILE, $people)) {
      send_json(500, ["ok"=>false,"code"=>500,"error"=>"SERVER_ERROR","message"=>"Failed to write storage."]);
    }
    send_json(200, ["ok"=>true,"code"=>200,"data"=>$people[$idx]]);
  }

  if ($method === "DELETE") {
    $id = intval($_GET["id"] ?? 0);
    if ($id <= 0) send_json(400, ["ok"=>false,"code"=>400,"error"=>"BAD_REQUEST","message"=>"Provide a valid id."]);
    $idx = find_person_index($people, $id);
    if ($idx < 0) send_json(404, ["ok"=>false,"code"=>404,"error"=>"NOT_FOUND","message"=>"Person not found."]);

    array_splice($people, $idx, 1);
    if (!write_json_file_atomic(DATA_FILE, $people)) {
      send_json(500, ["ok"=>false,"code"=>500,"error"=>"SERVER_ERROR","message"=>"Failed to write storage."]);
    }
    send_json(204, null);
  }

  send_json(404, ["ok"=>false,"code"=>404,"error"=>"NOT_FOUND","message"=>"Route not found."]);

} catch (Throwable $e) {
  send_json(500, ["ok"=>false,"code"=>500,"error"=>"SERVER_ERROR","message"=>"Unhandled server exception.","details"=>$e->getMessage()]);
}
?>