<?php
const DB_HOST = "127.0.0.1";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "attack_surface_lab";

const LAB_API_KEY = "lab123";

const MODE_FILE = __DIR__ . "/.lab_mode.json";

function lab_mode(): string {
  if (!file_exists(MODE_FILE)) return "VULNERABLE";
  $raw = @file_get_contents(MODE_FILE);
  if ($raw === false) return "VULNERABLE";
  $j = json_decode($raw, true);
  $m = is_array($j) && isset($j["mode"]) ? strtoupper((string)$j["mode"]) : "VULNERABLE";
  return in_array($m, ["VULNERABLE","HARDENED"], true) ? $m : "VULNERABLE";
}

function set_lab_mode(string $mode): bool {
  $mode = strtoupper($mode);
  if (!in_array($mode, ["VULNERABLE","HARDENED"], true)) return false;
  $payload = json_encode(["mode"=>$mode,"updated_at"=>date("c")], JSON_PRETTY_PRINT);
  return @file_put_contents(MODE_FILE, $payload) !== false;
}
