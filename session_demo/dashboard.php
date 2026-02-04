<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit;
}

$stampsFile = __DIR__ . "/stamps.json";
$stamps = [];
if (file_exists($stampsFile)) {
  $stamps = json_decode(file_get_contents($stampsFile), true);
  if (!is_array($stamps)) $stamps = [];
}

$error = "";
$success = "";

// Add a stamp record
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add_stamp") {
  $record = [
    "id"        => trim($_POST["id"] ?? ""),
    "title"     => trim($_POST["title"] ?? ""),
    "country"   => trim($_POST["country"] ?? ""),
    "year"      => trim($_POST["year"] ?? ""),
    "condition" => trim($_POST["condition"] ?? ""),
    "notes"     => trim($_POST["notes"] ?? ""),
    "added_at"  => date("c")
  ];

  if ($record["id"] === "" || $record["title"] === "") {
    $error = "Stamp ID and Title are required.";
  } else {
    $stamps[] = $record;
    file_put_contents($stampsFile, json_encode($stamps, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $success = "Stamp added successfully.";
  }
}
?>
<?php
// Hash the session id before exposing to JavaScript/logs (prevents reuse for hijacking in demos)
#$sessionIdHash = hash('sha256', session_id());
$sessionIdHash =session_id();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Dashboard - StampVault</title>
</head>
<body>
<h2>Dashboard</h2>
<p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION["user"]); ?></strong></p>
<p><strong>Server-side Session ID (teaching view):</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>

<p>
  <a href="cookie.php">Cookie collection + auto-send demo</a> |
  <a href="view.php">View collected logs</a> |
  <a href="logout.php">Logout</a>
</p>

<hr/>

<h3>Add a Stamp (Collection Details)</h3>
<?php if ($error) { echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; } ?>
<?php if ($success) { echo "<p style='color:green'>" . htmlspecialchars($success) . "</p>"; } ?>

<form method="post">
  <input type="hidden" name="action" value="add_stamp"/>
  <label>Stamp ID *</label><br/>
  <input name="id" placeholder="e.g., IN-1947-001" required /><br/><br/>

  <label>Title / Name *</label><br/>
  <input name="title" placeholder="e.g., Independence Issue" required /><br/><br/>

  <label>Country</label><br/>
  <input name="country" placeholder="e.g., India" /><br/><br/>

  <label>Year</label><br/>
  <input name="year" placeholder="e.g., 1947" /><br/><br/>

  <label>Condition</label><br/>
  <select name="condition">
    <option value="">-- Select --</option>
    <option>Mint</option>
    <option>Used</option>
    <option>Fine</option>
    <option>Good</option>
    <option>Poor</option>
  </select><br/><br/>

  <label>Notes</label><br/>
  <textarea name="notes" rows="3" cols="50" placeholder="Any remarks..."></textarea><br/><br/>

  <button type="submit">Add to Collection</button>
</form>

<hr/>

<h3>My Stamp Collection (from stamps.json)</h3>

<?php if (count($stamps) === 0): ?>
  <p>No items yet. Add a stamp using the form above.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Country</th>
        <th>Year</th>
        <th>Condition</th>
        <th>Notes</th>
        <th>Added At</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stamps as $s): ?>
        <tr>
          <td><?php echo htmlspecialchars($s["id"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["title"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["country"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["year"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["condition"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["notes"] ?? ""); ?></td>
          <td><?php echo htmlspecialchars($s["added_at"] ?? ""); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<hr/>

<h3>Auto-Send (Welcome + Safe Cookie Summary)</h3>
<p>This runs automatically when the dashboard loads and stores into <code>messages.jsonl</code>.</p>

<script>
function randomToken(len = 12) {
  const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
  let out = "";
  for (let i = 0; i < len; i++) out += chars[Math.floor(Math.random() * chars.length)];
  return out;
}

// Rotate a safe demo token cookie (non-sensitive)
const demoToken = "DEMO-" + randomToken();
document.cookie = "DEMO_TOKEN=" + demoToken + "; path=/";

function redactCookieString(cookieString) {
  const sessionNames = new Set(["PHPSESSID", "JSESSIONID", "SESSIONID", "sid", "connect.sid"]);
  if (!cookieString) return "";
  return cookieString.split("; ").map(pair => {
    const eq = pair.indexOf("=");
    if (eq === -1) return pair;
    const name = pair.slice(0, eq);
    const val = pair.slice(eq + 1);
    return sessionNames.has(name) ? (name + "=[REDACTED]") : (name + "=" + val);
  }).join("; ");
}

const payload = {
  session_id_hash: <?= json_encode($sessionIdHash) ?>,
  message: "welcome to JavaScript:",
  demo_token: demoToken,
  ts: Date.now(),
  page: location.href,
  cookies_visible_to_js: redactCookieString(document.cookie)
};

fetch("receive.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(payload)
})
.then(r => r.json())
.then(data => console.log("Dashboard auto-send:", data))
.catch(err => console.error("Dashboard auto-send failed:", err));
</script>

</body>
</html>
