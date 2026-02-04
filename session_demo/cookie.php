<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
?>
<?php
$sessionIdHash = hash('sha256', session_id());
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Cookie Collection Demo - StampVault</title>
</head>
<body>
<h2>Cookie Collection Demo</h2>
<p><strong>Server-side Session ID (teaching view):</strong> <code><?php echo htmlspecialchars(session_id()); ?></code></p>

<p>
This page demonstrates two things:
</p>
<ol>
  <li>A <strong>rotating demo identifier</strong> (DEMO_TOKEN) that changes on every page load (safe for teaching).</li>
  <li>What cookies are visible to JavaScript via <code>document.cookie</code> (HttpOnly cookies are not visible).</li>
</ol>

<pre id="out">Loading...</pre>

<script>
function randomToken(len = 12) {
  const chars = "abcdefghijklmnopqrstuvwxyz0123456789";
  let out = "";
  for (let i = 0; i < len; i++) out += chars[Math.floor(Math.random() * chars.length)];
  return out;
}

// Rotate a safe demo token cookie (non-sensitive) each load
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

const visible = document.cookie; // only non-HttpOnly cookies
const redacted = redactCookieString(visible);

document.getElementById("out").textContent =
  "Rotating DEMO_TOKEN (safe):\n" + demoToken +
  "\n\nCookies visible to JavaScript (raw):\n" + (visible || "[none]") +
  "\n\nCookies visible to JavaScript (redacted):\n" + (redacted || "[none]");

const payload = {
  session_id_hash: <?= json_encode($sessionIdHash) ?>,
  message: "welcome to JavaScript:",
  demo_token: demoToken,
  ts: Date.now(),
  page: location.href,
  cookies_visible_to_js: redacted
};

fetch("receive.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(payload)
})
.then(r => r.json())
.then(data => console.log("Cookie demo send:", data))
.catch(err => console.error("Cookie demo send failed:", err));
</script>

<p><a href="dashboard.php">Back to Dashboard</a> | <a href="view.php">View collected logs</a> | <a href="logout.php">Logout</a></p>
</body>
</html>
