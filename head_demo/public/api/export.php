<?php
require_once __DIR__ . '/_util.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
  if (!in_array($method, ['GET','HEAD'], true)) {
    header('Allow: GET, HEAD');
    util_log('export method not allowed');
    util_send_json(['ok' => false, 'message' => 'Method not allowed'], 405);
    exit;
  }

  $format = strtolower(trim((string)($_GET['format'] ?? 'csv')));
  if ($format !== 'csv') {
    util_send_json(['ok' => false, 'message' => 'Only format=csv supported'], 400);
    exit;
  }

  $items = util_load_items();
  $items = util_filter_items($items);

  $cols = ['id','type','name','country','year','denomination','condition','acquired_date','value_estimate','notes','created_at','updated_at'];

  $out = fopen('php://temp', 'r+');
  fputcsv($out, $cols);
  foreach ($items as $it) {
    $row = [];
    foreach ($cols as $c) $row[] = $it[$c] ?? '';
    fputcsv($out, $row);
  }
  rewind($out);
  $csv = stream_get_contents($out);
  fclose($out);

  header('Cache-Control: no-store');
  util_log('export csv');
  util_send_csv($csv, 'collection_export.csv', 200);

} catch (Throwable $e) {
  util_log('export server error: '.$e->getMessage());
  util_send_json(['ok' => false, 'message' => 'Server error'], 500);
}
