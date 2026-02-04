<?php
require_once __DIR__ . "/../config.php";

header("Content-Type: text/html; charset=UTF-8");

if (!is_localhost()) {
  http_response_code(403);
  echo "Forbidden (lab_toggle is localhost-only).";
  exit;
}

$cfg_path = __DIR__ . "/../lab_mode.json";
$allowed = ["BYPASS", "HARDCODED", "NO_RATELIMIT"];

// Quick switch: /ui/lab_toggle.php?quick=BYPASS|HARDCODED|NO_RATELIMIT
if (isset($_GET["quick"])) {
  $q = strtoupper(trim((string)$_GET["quick"]));
  if (in_array($q, $allowed, true)) {
    $new_cfg = ["LAB_MODE" => true, "LAB_SCENARIO" => $q];
    $json = json_encode($new_cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json !== false) @file_put_contents($cfg_path, $json);
  }
  header("Location: /wss/attack_surface_auth_lab/ui/login.php");
  exit;
}

$current = ["LAB_MODE" => false, "LAB_SCENARIO" => "BYPASS"];
$msg = "";

if (file_exists($cfg_path)) {
  $raw = file_get_contents($cfg_path);
  $arr = json_decode($raw, true);
  if (is_array($arr)) {
    $current["LAB_MODE"] = (bool)($arr["LAB_MODE"] ?? false);
    $current["LAB_SCENARIO"] = strtoupper((string)($arr["LAB_SCENARIO"] ?? "BYPASS"));
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $mode = isset($_POST["LAB_MODE"]) ? true : false;
  $sc   = strtoupper(trim((string)($_POST["LAB_SCENARIO"] ?? "BYPASS")));
  if (!in_array($sc, $allowed, true)) $sc = "BYPASS";

  $new_cfg = ["LAB_MODE" => $mode, "LAB_SCENARIO" => $sc];
  $json = json_encode($new_cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

  if ($json === false) $msg = "Failed to encode JSON.";
  else {
    $ok = file_put_contents($cfg_path, $json);
    if ($ok === false) $msg = "Failed to write lab_mode.json (permissions).";
    else { $current = $new_cfg; $msg = "Updated successfully."; }
  }
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lab Toggle</title>
  <style>
    body{font-family:Arial,sans-serif;padding:20px}
    .card{max-width:760px;border:1px solid #ddd;border-radius:10px;padding:16px}
    .msg{margin:12px 0}
    .ok{color:#0b6}
    .bad{color:#b00020}
    code{background:#f6f6f6;padding:2px 6px;border-radius:6px}
    select{padding:6px}
    button{padding:8px 12px}
    .btn{display:inline-block;padding:10px 14px;border:1px solid #ccc;border-radius:10px;margin:6px 8px 6px 0;text-decoration:none}
  </style>
</head>
<body>
<div class="card">
  <h2>To Toggle</h2>
  <!--<p>Config: <code><?= h($cfg_path) ?></code></p> -->
  <p>Current: <b>LAB MODE=<?= $current["LAB_MODE"] ? "true" : "false" ?></b>,
     <b>LAB SCENARIO=<?= h($current["LAB_SCENARIO"]) ?></b></p>

  <?php if ($msg): ?>
    <div class="msg <?= (str_contains($msg, "Perfect") ? "ok" : "bad") ?>"><b><?= h($msg) ?></b></div>
  <?php endif; ?>

  <form method="POST">
    <label><input type="checkbox" name="LAB_MODE" <?= $current["LAB_MODE"] ? "checked" : "" ?>> Enable LAB_MODE</label>
    <div style="margin-top:12px">
      <label>Scenario:</label><br>
      <select name="LAB_SCENARIO">
        <?php foreach ($allowed as $s): ?>
          <option value="<?= h($s) ?>" <?= ($current["LAB_SCENARIO"] === $s) ? "selected" : "" ?>><?= h($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="margin-top:12px">
      <button type="submit">Save</button>
      <a style="margin-left:10px" href="/wss/attack_surface_auth_lab/ui/login.php">Go to Login</a>
      <a style="margin-left:10px" href="/wss/attack_surface_auth_lab/">Home</a>
    </div>
  </form>

  <hr>
  <h3>Direct vulnerable login pages</h3>
  <p>These switch the scenario automatically and open the login UI:</p>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/bypass_login.php">BYPASS Login</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/hardcoded_login.php">HARDCODED Login</a>
  <a class="btn" href="/wss/attack_surface_auth_lab/ui/noratelimit_login.php">NO_RATELIMIT Login</a>
</div>
</body>
</html>
