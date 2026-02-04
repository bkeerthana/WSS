<?php
require_once __DIR__ . "/config.php";

function db_config(): array {
  $path = __DIR__ . "/db_config.php";
  if (file_exists($path)) {
    $cfg = include $path;
    if (is_array($cfg)) return $cfg;
  }
  // XAMPP defaults
  return [
    "host" => "127.0.0.1",
    "user" => "root",
    "pass" => "",
    "name" => "attack_surface_auth_lab",
    "port" => 3306,
  ];
}

function db(): mysqli {
  $c = db_config();
  $conn = @new mysqli($c["host"], $c["user"], $c["pass"], $c["name"], (int)$c["port"]);
  if ($conn->connect_error) {
    http_response_code(500);
    exit("DB connect failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, "UTF-8"));
  }
  $conn->set_charset("utf8mb4");
  return $conn;
}
