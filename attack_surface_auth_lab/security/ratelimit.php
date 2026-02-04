<?php
require_once __DIR__ . "/../config.php";

function rl_key(string $username): string {
  $ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";
  return sha1($ip . "|" . strtolower($username));
}

function rl_check(string $username): array {
  if (lab_mode() && lab_scenario() === "NO_RATELIMIT") {
    return [true, ""];
  }

  if (session_status() === PHP_SESSION_NONE) session_start();

  $now = time();
  $data = $_SESSION["rl"] ?? [];
  $k = rl_key($username);

  $rec = $data[$k] ?? ["count" => 0, "first" => $now, "locked_until" => 0];

  if ($rec["locked_until"] > $now) {
    $wait = $rec["locked_until"] - $now;
    return [false, "Too many attempts. Try again in {$wait}s."];
  }

  if (($now - $rec["first"]) > 60) {
    $rec = ["count" => 0, "first" => $now, "locked_until" => 0];
  }

  if ($rec["count"] >= 5) {
    $rec["locked_until"] = $now + 30;
    $data[$k] = $rec;
    $_SESSION["rl"] = $data;
    return [false, "Too many attempts. Locked for 30 seconds."];
  }

  $data[$k] = $rec;
  $_SESSION["rl"] = $data;
  return [true, ""];
}

function rl_fail(string $username): void {
  if (lab_mode() && lab_scenario() === "NO_RATELIMIT") return;

  if (session_status() === PHP_SESSION_NONE) session_start();

  $now = time();
  $data = $_SESSION["rl"] ?? [];
  $k = rl_key($username);

  $rec = $data[$k] ?? ["count" => 0, "first" => $now, "locked_until" => 0];
  $rec["count"]++;

  $data[$k] = $rec;
  $_SESSION["rl"] = $data;
}
