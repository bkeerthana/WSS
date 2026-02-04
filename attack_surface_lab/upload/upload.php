<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";
require_login();
header("Content-Type: text/html; charset=UTF-8");

$mode = lab_mode();
$uploadDir = __DIR__ . "/uploads";
@mkdir($uploadDir, 0777, true);

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_FILES['file'])) {
    $msg = "No file received.";
  } else {
    $f = $_FILES['file'];
    $maxBytes = 2 * 1024 * 1024;

    $allowedExt = ($mode === 'VULNERABLE')
      ? ['txt','png','jpg','jpeg','pdf','svg']
      : ['txt','png','jpg','jpeg','pdf'];

    $orig = $f['name'] ?? 'upload.bin';
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

    if ($f['size'] > $maxBytes) $msg = "Rejected: too large.";
    elseif (!in_array($ext, $allowedExt, true)) $msg = "Rejected: extension not allowed.";
    else {
      $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig);
      $dest = $uploadDir . "/" . $safeName;
      if (move_uploaded_file($f['tmp_name'], $dest)) $msg = "Uploaded: " . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8');
      else $msg = "Upload failed.";
    }
  }
}
?>
<h3>Upload</h3>
<p>Mode: <b><?= htmlspecialchars($mode) ?></b> | <a href="../mode.php">Switch</a> | <a href="../ui/dashboard.php">Dashboard</a></p>

<form method="POST" enctype="multipart/form-data">
  <input type="file" name="file" required>
  <button>Upload</button>
</form>

<p><b>Status:</b> <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
<p>Uploads directory: <code>/wss/attack_surface_lab/upload/uploads/</code></p>
