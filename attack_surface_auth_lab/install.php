\
<?php
require_once __DIR__ . "/config.php";

header("Content-Type: text/html; charset=UTF-8");

if (!is_localhost()) {
  http_response_code(403);
  exit("Forbidden: installer is localhost-only.");
}

$msg = "";
$err = "";

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

function do_install(string $host, int $port, string $user, string $pass, string $name): array {
  $conn = @new mysqli($host, $user, $pass, "", $port);
  if ($conn->connect_error) {
    return ["", "MySQL connect failed: " . $conn->connect_error];
  }
  $conn->set_charset("utf8mb4");

  if (!$conn->query("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    return ["", "Create DB failed: " . $conn->error];
  }
  if (!$conn->select_db($name)) {
    return ["", "Select DB failed: " . $conn->error];
  }

  $sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB;";

  if (!$conn->query($sql_users)) {
    return ["", "Create table users failed: " . $conn->error];
  }

  $res = $conn->query("SELECT COUNT(*) AS c FROM users");
  $row = $res ? $res->fetch_assoc() : ["c" => 0];
  $count = (int)($row["c"] ?? 0);

  if ($count === 0) {
    $seed = [
      ["anu","student","anu@123"],
      ["ruban","student","ruban@123"],
      ["keerthana","admin","keerthana@123"],
    ];
    $stmt = $conn->prepare("INSERT INTO users (username, role, password_hash) VALUES (?,?,?)");
    foreach ($seed as $u) {
      $hash = password_hash($u[2], PASSWORD_DEFAULT);
      $stmt->bind_param("sss", $u[0], $u[1], $hash);
      $stmt->execute();
    }
  }

  $cfg_arr = ["host"=>$host,"user"=>$user,"pass"=>$pass,"name"=>$name,"port"=>$port];
  $cfg_php = "<?php\nreturn " . var_export($cfg_arr, true) . ";\n";
  if (file_put_contents(__DIR__ . "/db_config.php", $cfg_php) === false) {
    return ["", "Install OK but failed writing db_config.php (permissions)."];
  }

  return ["✅ Installed successfully. Go to Login.", ""];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $action = (string)($_POST["action"] ?? "custom");

  if ($action === "quick") {
    [$msg, $err] = do_install("127.0.0.1", 3306, "root", "", "attack_surface_auth_lab");
  } else {
    $host = trim((string)($_POST["host"] ?? "127.0.0.1"));
    $port = (int)($_POST["port"] ?? 3306);
    $user = trim((string)($_POST["user"] ?? "root"));
    $pass = (string)($_POST["pass"] ?? "");
    $name = trim((string)($_POST["name"] ?? "attack_surface_auth_lab"));
    [$msg, $err] = do_install($host, $port, $user, $pass, $name);
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Installer — Attack Surface Auth Lab</title>
  <style>
    body{font-family:Arial,sans-serif;margin:24px}
    .card{max-width:760px;border:1px solid #ddd;border-radius:10px;padding:16px}
    input{padding:8px;width:100%;max-width:360px}
    .row{margin:10px 0}
    .err{color:#b00020}
    .ok{color:#0b6}
    .btn{padding:10px 14px}
    code{background:#f6f6f6;padding:2px 6px;border-radius:6px}
    details{margin-top:14px}
  </style>
</head>
<body>
<div class="card">
  <h2>Attack Surface Auth Lab — One-Click Installer</h2>
  <p><b>Quick Install</b> uses XAMPP defaults: <code>root</code> with empty password, DB: <code>attack_surface_auth_lab</code>.</p>

  <?php if ($err): ?><p class="err"><b><?= h($err) ?></b></p><?php endif; ?>
  <?php if ($msg): ?><p class="ok"><b><?= h($msg) ?></b></p><?php endif; ?>

  <form method="POST" style="margin:12px 0">
    <input type="hidden" name="action" value="quick">
    <button class="btn" type="submit">✅ Quick Install (XAMPP default)</button>
    <a style="margin-left:10px" href="/wss/attack_surface_auth_lab/ui/login.php">Go to Login</a>
  </form>

  <details>
    <summary><b>Custom Install</b> (if your MySQL credentials are different)</summary>
    <form method="POST" style="margin-top:10px">
      <input type="hidden" name="action" value="custom">
      <div class="row"><label>MySQL Host<br><input name="host" value="<?= h($_POST["host"] ?? "127.0.0.1") ?>"></label></div>
      <div class="row"><label>Port<br><input name="port" value="<?= h($_POST["port"] ?? "3306") ?>"></label></div>
      <div class="row"><label>User<br><input name="user" value="<?= h($_POST["user"] ?? "root") ?>"></label></div>
      <div class="row"><label>Password<br><input name="pass" type="password" value="<?= h($_POST["pass"] ?? "") ?>"></label></div>
      <div class="row"><label>Database Name<br><input name="name" value="<?= h($_POST["name"] ?? "attack_surface_auth_lab") ?>"></label></div>
      <button class="btn" type="submit">Install</button>
    </form>
  </details>

  <hr>
  <p>After install, open:</p>
  <ul>
    <li><code>/wss/attack_surface_auth_lab/ui/login.php</code></li>
    <li><code>/wss/attack_surface_auth_lab/ui/lab_toggle.php</code> (localhost-only)</li>
  </ul>
</div>
</body>
</html>
