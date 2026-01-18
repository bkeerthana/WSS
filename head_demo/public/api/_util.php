<?php

function util_send_json($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  // For HEAD requests, we send headers only.
  if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') {
    return;
  }
  echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function util_send_csv(string $csv, string $filename = 'export.csv', int $status = 200): void {
  http_response_code($status);
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="'.$filename.'"');
  if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') {
    return;
  }
  echo $csv;
}

function util_read_json_body(): array {
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function util_data_path(): string {
  return __DIR__ . '/../data/collection.json';
}

function util_log(string $message): void {
  $logFile = __DIR__ . '/../logs/app.log';
  $ts = date('Y-m-d H:i:s');
  $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
  $method = $_SERVER['REQUEST_METHOD'] ?? '-';
  $uri = $_SERVER['REQUEST_URI'] ?? '-';
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
  $line = "[$ts] $ip $method $uri | $message | UA=\"$ua\"\n";
  @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

function util_load_items(): array {
  $path = util_data_path();
  if (!file_exists($path)) {
    return [];
  }
  $json = file_get_contents($path);
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function util_save_items(array $items): bool {
  $path = util_data_path();
  $dir = dirname($path);
  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  $json = json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  return (bool)@file_put_contents($path, $json . "\n", LOCK_EX);
}

function util_uuid(): string {
  // Simple unique id for demo use.
  return bin2hex(random_bytes(8));
}

function util_validate_item(array $p, bool $is_update = false): array {
  $errors = [];
  $type = strtolower(trim((string)($p['type'] ?? '')));
  if (!in_array($type, ['stamp','coin'], true)) $errors[] = 'type must be stamp or coin';
  $name = trim((string)($p['name'] ?? ''));
  if ($name === '') $errors[] = 'name is required';

  $year = $p['year'] ?? null;
  if ($year !== null && $year !== '' && (!is_numeric($year) || (int)$year < 1500 || (int)$year > 2100)) {
    $errors[] = 'year must be between 1500 and 2100';
  }

  $value = $p['value_estimate'] ?? null;
  if ($value !== null && $value !== '' && (!is_numeric($value) || (float)$value < 0)) {
    $errors[] = 'value_estimate must be a non-negative number';
  }

  if ($is_update) {
    $id = trim((string)($p['id'] ?? ''));
    if ($id === '') $errors[] = 'id is required for update';
  }

  return $errors;
}

function util_filter_items(array $items): array {
  $q = trim((string)($_GET['q'] ?? ''));
  $type = trim((string)($_GET['type'] ?? ''));
  $country = trim((string)($_GET['country'] ?? ''));
  $year = trim((string)($_GET['year'] ?? ''));

  $q_l = mb_strtolower($q);
  $type_l = mb_strtolower($type);
  $country_l = mb_strtolower($country);

  return array_values(array_filter($items, function($it) use ($q_l, $type_l, $country_l, $year) {
    if ($type_l && mb_strtolower((string)($it['type'] ?? '')) !== $type_l) return false;
    if ($country_l && mb_strtolower((string)($it['country'] ?? '')) !== $country_l) return false;
    if ($year && (string)($it['year'] ?? '') !== (string)$year) return false;

    if ($q_l) {
      $hay = mb_strtolower(
        (string)($it['name'] ?? '').' '.
        (string)($it['notes'] ?? '').' '.
        (string)($it['denomination'] ?? '').' '.
        (string)($it['country'] ?? '')
      );
      if (mb_strpos($hay, $q_l) === false) return false;
    }
    return true;
  }));
}
