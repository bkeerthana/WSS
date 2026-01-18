<?php
require_once __DIR__ . '/_util.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
  if ($method === 'GET' || $method === 'HEAD') {
    $items = util_load_items();
    $filtered = util_filter_items($items);

    // A simple caching hint for demo purposes.
    header('Cache-Control: no-store');
    util_log('list items');
    util_send_json(['ok' => true, 'items' => $filtered]);
    exit;
  }

  if ($method === 'POST') {
    $p = util_read_json_body();
    $errors = util_validate_item($p, false);
    if ($errors) {
      util_log('create failed: '.implode('; ', $errors));
      util_send_json(['ok' => false, 'message' => implode(', ', $errors)], 400);
      exit;
    }

    $items = util_load_items();
    $id = util_uuid();
    $item = [
      'id' => $id,
      'type' => strtolower(trim((string)$p['type'])),
      'name' => trim((string)$p['name']),
      'country' => trim((string)($p['country'] ?? '')),
      'year' => ($p['year'] === null || $p['year'] === '') ? null : (int)$p['year'],
      'denomination' => trim((string)($p['denomination'] ?? '')),
      'condition' => strtolower(trim((string)($p['condition'] ?? 'good'))),
      'acquired_date' => ($p['acquired_date'] ?? null) ?: null,
      'value_estimate' => ($p['value_estimate'] === null || $p['value_estimate'] === '') ? null : (float)$p['value_estimate'],
      'notes' => trim((string)($p['notes'] ?? '')),
      'created_at' => gmdate('c'),
      'updated_at' => gmdate('c'),
    ];

    $items[] = $item;
    if (!util_save_items($items)) {
      util_log('create failed: storage write error');
      util_send_json(['ok' => false, 'message' => 'Storage write error'], 500);
      exit;
    }

    util_log('created item '.$id);
    util_send_json(['ok' => true, 'item' => $item], 201);
    exit;
  }

  if ($method === 'PUT') {
    $p = util_read_json_body();
    $errors = util_validate_item($p, true);
    if ($errors) {
      util_log('update failed: '.implode('; ', $errors));
      util_send_json(['ok' => false, 'message' => implode(', ', $errors)], 400);
      exit;
    }

    $items = util_load_items();
    $id = trim((string)$p['id']);
    $found = false;

    foreach ($items as &$it) {
      if (($it['id'] ?? '') === $id) {
        $found = true;
        $it['type'] = strtolower(trim((string)$p['type']));
        $it['name'] = trim((string)$p['name']);
        $it['country'] = trim((string)($p['country'] ?? ''));
        $it['year'] = ($p['year'] === null || $p['year'] === '') ? null : (int)$p['year'];
        $it['denomination'] = trim((string)($p['denomination'] ?? ''));
        $it['condition'] = strtolower(trim((string)($p['condition'] ?? 'good')));
        $it['acquired_date'] = ($p['acquired_date'] ?? null) ?: null;
        $it['value_estimate'] = ($p['value_estimate'] === null || $p['value_estimate'] === '') ? null : (float)$p['value_estimate'];
        $it['notes'] = trim((string)($p['notes'] ?? ''));
        $it['updated_at'] = gmdate('c');
        break;
      }
    }
    unset($it);

    if (!$found) {
      util_log('update failed: not found '.$id);
      util_send_json(['ok' => false, 'message' => 'Item not found'], 404);
      exit;
    }

    if (!util_save_items($items)) {
      util_log('update failed: storage write error');
      util_send_json(['ok' => false, 'message' => 'Storage write error'], 500);
      exit;
    }

    util_log('updated item '.$id);
    util_send_json(['ok' => true, 'message' => 'Updated']);
    exit;
  }

  if ($method === 'DELETE') {
    $p = util_read_json_body();
    $id = trim((string)($p['id'] ?? ''));
    if ($id === '') {
      util_log('delete failed: id missing');
      util_send_json(['ok' => false, 'message' => 'id is required'], 400);
      exit;
    }

    $items = util_load_items();
    $before = count($items);
    $items = array_values(array_filter($items, fn($it) => (string)($it['id'] ?? '') !== $id));

    if (count($items) === $before) {
      util_log('delete failed: not found '.$id);
      util_send_json(['ok' => false, 'message' => 'Item not found'], 404);
      exit;
    }

    if (!util_save_items($items)) {
      util_log('delete failed: storage write error');
      util_send_json(['ok' => false, 'message' => 'Storage write error'], 500);
      exit;
    }

    util_log('deleted item '.$id);
    util_send_json(['ok' => true, 'message' => 'Deleted']);
    exit;
  }

  // For other methods, reply clearly.
  header('Allow: GET, HEAD, POST, PUT, DELETE');
  util_log('method not allowed');
  util_send_json(['ok' => false, 'message' => 'Method not allowed'], 405);

} catch (Throwable $e) {
  util_log('server error: '.$e->getMessage());
  util_send_json(['ok' => false, 'message' => 'Server error'], 500);
}
