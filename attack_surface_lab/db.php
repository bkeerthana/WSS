<?php
require_once __DIR__ . "/config.php";

function db_server(): mysqli {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
  if ($conn->connect_error) {
    http_response_code(500);
    die("MySQL connection failed");
  }
  $conn->set_charset("utf8mb4");
  return $conn;
}

function db(): mysqli {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($conn->connect_error) {
    http_response_code(500);
    die("DB connection failed");
  }
  $conn->set_charset("utf8mb4");
  return $conn;
}
