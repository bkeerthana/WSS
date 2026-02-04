<?php
// config.php — file-based lab toggle (no Apache config changes)

function lab_config(): array {
  $path = __DIR__ . "/lab_mode.json";
  $default = ["LAB_MODE" => false, "LAB_SCENARIO" => "BYPASS"];
  if (!file_exists($path)) return $default;

  $raw = file_get_contents($path);
  if ($raw === false) return $default;

  $cfg = json_decode($raw, true);
  if (!is_array($cfg)) return $default;

  return [
    "LAB_MODE" => (bool)($cfg["LAB_MODE"] ?? false),
    "LAB_SCENARIO" => (string)($cfg["LAB_SCENARIO"] ?? "BYPASS"),
  ];
}

function lab_mode(): bool {
  $cfg = lab_config();
  return $cfg["LAB_MODE"] === true;
}

function lab_scenario(): string {
  $cfg = lab_config();
  $sc = strtoupper(trim((string)($cfg["LAB_SCENARIO"] ?? "BYPASS")));
  $allowed = ["BYPASS", "HARDCODED", "NO_RATELIMIT"];
  return in_array($sc, $allowed, true) ? $sc : "BYPASS";
}

function is_localhost(): bool {
  $ip = $_SERVER["REMOTE_ADDR"] ?? "";
  return ($ip === "127.0.0.1" || $ip === "::1");
}
