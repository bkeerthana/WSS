<?php
header("Content-Type: text/html; charset=UTF-8");

$file = __DIR__ . "/messages.jsonl";
$rows = [];

if (file_exists($file)) {
  $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    $obj = json_decode($line, true);
    if (is_array($obj)) $rows[] = $obj;
  }
}

// Show newest first
$rows = array_reverse($rows);

// Optional: limit display to last 200 records
$rows = array_slice($rows, 0, 200);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Collected Logs - StampVault</title>
</head>
<body>
<h2>Collected Logs (messages.jsonl)</h2>

<p>
  <a href="index.php">Home</a> |
  <a href="login.php">Login</a> |
  <a href="dashboard.php">Dashboard</a> |
  <a href="cookie.php">Cookie Demo</a>
</p>

<?php if (count($rows) === 0): ?>
  <p>No logs yet. Visit <code>dashboard.php</code> or <code>cookie.php</code> to generate entries.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>Received At</th>
        <th>Message</th>
        <th>Session ID Hash (SHA-256)</th>
        <th>Demo Token</th>
        <th>Page</th>
        <th>Remote Addr</th>
        <th>User Agent</th>
        <th>Cookies Visible to JS (Redacted)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?php echo htmlspecialchars($r["received_at"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($r["message"] ?? ""); ?></td>
          <td style="max-width: 450px; word-wrap: break-word;"><?php echo htmlspecialchars($r["session_id_hash"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($r["demo_token"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($r["page"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($r["remote_addr"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($r["user_agent"] ?? ""); ?></td>
          <td style="max-width: 450px; word-wrap: break-word;">
            <?php echo htmlspecialchars($r["cookies_visible_to_js"] ?? ""); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

</body>
</html>
