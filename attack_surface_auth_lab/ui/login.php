<?php
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../security/ratelimit.php";

header("Content-Type: text/html; charset=UTF-8");

$err = "";
$msg = "";
if (isset($_GET["err"])) $err = (string)$_GET["err"];
if (isset($_GET["msg"])) $msg = (string)$_GET["msg"];

$mode = lab_mode();
$sc   = $mode ? lab_scenario() : "SECURE";

// BYPASS scenario: demo link
if ($mode && $sc === "BYPASS" && isset($_GET["demo"])) {
  login_user(["id"=>0,"username"=>"demo","role"=>"student"]);
  header("Location: /wss/attack_surface_auth_lab/ui/dashboard.php?msg=" . urlencode("LAB: Auth bypass"));
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim((string)($_POST["username"] ?? ""));
  $password = (string)($_POST["password"] ?? "");

  if ($username === "" || $password === "") {
    $err = "Username and password required.";
  } else {

    // Scenario 1: BYPASS (lab-only)
    if ($mode && $sc === "BYPASS") {
      login_user(["id"=>0,"username"=>$username ?: "guest","role"=>"student"]);
      header("Location: /wss/attack_surface_auth_lab/ui/dashboard.php?msg=" . urlencode("LAB: Auth bypass"));
      exit;
    }

    // Scenario 2: HARDCODED (lab-only)
    if ($mode && $sc === "HARDCODED") {
      $hard_user = "demo";
      $hard_pass = "demo123";
      if ($username === $hard_user && $password === $hard_pass) {
        login_user(["id"=>999,"username"=>$hard_user,"role"=>"student"]);
        header("Location: /wss/attack_surface_auth_lab/ui/dashboard.php?msg=" . urlencode("LAB: Hardcoded credentials"));
        exit;
      } else {
        $err = "Invalid credentials (hardcoded demo).";
      }
    }

    // Scenario 3: rate limiting demo (NO_RATELIMIT disables lockout)
    if (!$err) {
      list($allowed, $rl_msg) = rl_check($username);
      if (!$allowed) {
        $err = $rl_msg;
      } else {
        $conn = db();
        $stmt = $conn->prepare("SELECT id, username, role, password_hash FROM users WHERE username = ? LIMIT 1");
        if (!$stmt) {
          $err = "DB error: prepare failed.";
        } else {
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $res = $stmt->get_result();
          $row = $res ? $res->fetch_assoc() : null;

          if (!$row) {
            $err = "Invalid credentials.";
            rl_fail($username);
          } else {
            $hash = (string)($row["password_hash"] ?? "");
            if ($hash === "" || !password_verify($password, $hash)) {
              $err = "Invalid credentials.";
              rl_fail($username);
            } else {
              login_user([
                "id" => (int)$row["id"],
                "username" => (string)$row["username"],
                "role" => (string)$row["role"],
              ]);
              header("Location: /wss/attack_surface_auth_lab/ui/dashboard.php");
              exit;
            }
          }
        }
      }
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $mode ? "Vulnerable Login — {$sc}" : "Login" ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .card { max-width: 560px; border: 1px solid #ddd; border-radius: 10px; padding: 16px; }
    input { width: 100%; padding: 10px; margin: 8px 0; }
    button { padding: 10px 14px; }
    .err { color: #b00020; }
    .msg { color: #0b6; }
    .danger { background:#ffeaea; border:1px solid #d33; padding:10px; border-radius:8px; margin-bottom:12px; }
    .banner { background:#fff3cd; border:1px solid #ffeeba; padding:10px; border-radius:8px; margin-bottom:12px; }
    .hint { color:#555; }
    a { text-decoration:none; }
  </style>
</head>
<body>
  <div class="card">
    <h3>Login</h3>

    <?php if ($mode): ?>
      <div class="danger">
        <b>LAB: Authentication </b> — Scenario: <b><?= htmlspecialchars($sc, ENT_QUOTES, "UTF-8") ?></b>
      </div>

      <?php if ($sc === "BYPASS"): ?>
        <p class="hint">This login intentionally allows dashboard access without validating credentials.</p>
       <!-- <p><a href="/wss/attack_surface_auth_lab/ui/login.php?demo=1">▶ Continue without Login</a></p> -->
      <?php elseif ($sc === "HARDCODED"): ?>
        <p class="hint">This build contains credentials embedded in code.</p>
       <!-- <p class="hint">Try: <b>demo / demo123</b></p> -->
      <?php elseif ($sc === "NO_RATELIMIT"): ?>
        <p class="hint">This login disables lockout/rate limiting to demonstrate the risk of unlimited attempts.</p>
      <?php endif; ?>
    <?php else: ?>
      <div class="banner"><b>SECURE MODE</b> — Standard authentication with lockout.</div>
    <?php endif; ?>

    <?php if ($msg): ?><p class="msg"><b><?= htmlspecialchars($msg, ENT_QUOTES, "UTF-8") ?></b></p><?php endif; ?>
    <?php if ($err): ?><p class="err"><b><?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?></b></p><?php endif; ?>

    <form method="POST">
      <label>Username</label>
      <input name="username" autocomplete="username" required>

      <label>Password</label>
      <input name="password" type="password" autocomplete="current-password" required>

      <button type="submit">Login</button>
    </form>

    <p style="margin-top:12px">
      <a href="/wss/attack_surface_auth_lab/ui/register.php">Register</a>
      | <a href="/wss/attack_surface_auth_lab/">Home</a>
      <?php if ($mode): ?>
       | <a href="/wss/attack_surface_auth_lab/ui/lab_toggle.php">To Toggle</a> 
      <?php endif; ?>
    </p>
  </div>
</body>
</html>
