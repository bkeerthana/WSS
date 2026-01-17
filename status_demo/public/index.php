<?php ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Interactive CRUD + HTTP Status Codes Lab</title>
  <style>
    :root{
      --bg:#f6f7fb; --card:#ffffff; --text:#0f172a; --muted:#475569;
      --line:#e5e7eb; --accent:#0f172a; --soft:#eef2ff;
    }
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--text);}
    header{background:var(--accent);color:#fff;padding:18px 20px;}
    header h1{margin:0;font-size:18px;font-weight:800}
    header p{margin:6px 0 0;color:#cbd5e1;font-size:13px;line-height:1.35}
    .wrap{max-width:1200px;margin:18px auto;padding:0 14px;}
    .grid{display:grid;grid-template-columns: 460px 1fr;gap:14px;}
    .card{background:var(--card);border:1px solid var(--line);border-radius:14px;box-shadow:0 1px 2px rgba(0,0,0,.04);}
    .card h2{margin:0;padding:14px 14px 10px;font-size:14px;border-bottom:1px solid #eef2f7;}
    .body{padding:14px;}
    label{display:block;font-size:12px;color:#334155;margin:10px 0 6px;}
    input,select,textarea{width:100%;box-sizing:border-box;padding:10px 10px;border:1px solid #d1d5db;border-radius:12px;font-size:13px;background:#fff;}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .btn{background:var(--accent);color:#fff;border:none;border-radius:12px;padding:10px 12px;font-weight:800;font-size:13px;cursor:pointer;}
    .btn.secondary{background:#334155;}
    .btn:active{transform:translateY(1px);}
    .hint{font-size:12px;color:var(--muted);line-height:1.4;margin-top:8px;}
    .pill{display:inline-block;background:var(--soft);color:#3730a3;border:1px solid #c7d2fe;font-size:11px;padding:4px 8px;border-radius:999px;margin:0 6px 6px 0;}
    table{width:100%;border-collapse:collapse;font-size:13px;}
    th,td{border-bottom:1px solid #eef2f7;padding:8px 6px;text-align:left;vertical-align:top;}
    th{color:#334155;font-size:12px;text-transform:uppercase;letter-spacing:.04em;}
    .kpi{display:flex;gap:10px;flex-wrap:wrap}
    .kpi .box{border:1px solid var(--line);border-radius:14px;padding:10px 12px;background:#fff}
    .kpi .box b{display:block;font-size:12px;color:#334155}
    .kpi .box span{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px}
    .note{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12;padding:10px 12px;border-radius:14px;font-size:12px;line-height:1.4;}
    .hidden{display:none;}
    .divider{border:none;border-top:1px solid #eef2f7;margin:14px 0;}
  </style>
</head>
<body>
<header>
  <h1>Interactive CRUD + HTTP Response Codes (PHP/XAMPP)</h1>
  <p> Choose an operation, enter values, submit, and verify the <b>Status Code</b> + <b>Headers</b> in <b>DevTools → Network</b>. </p>
</header>

<div class="wrap">
  <div class="grid">
    <div class="card">
      <h2>1) Choose Operation</h2>
      <div class="body">
        <div class="note">
          Keep <b>DevTools → Network</b> open.
        </div>

        <label> This is for Authentication (demo 401/403)</label>
        <input id="apiKey" value="lab123" />
        <div class="hint"> Hint to get this error code : Empty → <b>401</b> (Authenticate). Wrong key → <b>403</b>.</div>

        <label>Operation</label>
        <select id="op" onchange="renderOp()">
          <option value="list" selected>List all people (GET → 200)</option>
          <option value="create">Create a person (POST → 201/409/422/415)</option>
          <option value="search">Search by name/phone/friend (GET → 200 or 404)</option>
          <option value="update">Update a person (PATCH/PUT → 200/404/422/415/409)</option>
          <option value="delete">Delete a person (DELETE → 204/404)</option>
        </select>

        <hr class="divider">

        <!-- CREATE -->
        <div id="panelCreate" class="hidden">
          <label>Name</label>
          <input id="cName" placeholder="e.g., Student1" />
          <label>Phone (10 digits)</label>
          <input id="cPhone" placeholder="e.g., 9000011111" />
          <label>Friends (comma-separated)</label>
          <input id="cFriends" placeholder="e.g., Ravi, Asha" />
          <div class="hint">Triggers: 422 (bad phone/empty name), 409 (duplicate phone), 201 (created).</div>
        </div>

        <!-- SEARCH -->
        <div id="panelSearch" class="hidden">
          <label>Search value</label>
          <input id="sQ" placeholder="Search by name/phone/friend" />
          <div class="hint">If no match, server returns <b>404 (no body)</b> for teaching.</div>
        </div>

        <!-- UPDATE -->
        <div id="panelUpdate" class="hidden">
          <label>Person ID</label>
          <input id="uId" placeholder="e.g., 1" />
          <label>Update mode</label>
          <select id="uMode">
            <option value="PATCH" selected>PATCH (partial update)</option>
            <option value="PUT">PUT (full replace)</option>
          </select>
          <div class="row" style="margin-top:10px;">
            <div>
              <label>New phone (optional)</label>
              <input id="uPhone" placeholder="10 digits" />
            </div>
            <div>
              <label>New name (optional)</label>
              <input id="uName" placeholder="Name" />
            </div>
          </div>
          <label>Friends (comma-separated)</label>
          <input id="uFriends" placeholder="e.g., Keerthi, Ravi" />
          <div class="hint">
            PATCH: provide any one field. PUT: provide name, phone, friends. Friends are converted to an array automatically.
          </div>
        </div>

        <!-- DELETE -->
        <div id="panelDelete" class="hidden">
          <label>Person ID</label>
          <input id="dId" placeholder="e.g., 2" />
        </div>

        <button class="btn" style="margin-top:12px;width:100%;" onclick="submitOp()">Submit</button>
        <div class="hint" style="margin-top:10px;">
          Codes you should observe:
          <span class="pill">200</span><span class="pill">201</span><span class="pill">204</span>
          <span class="pill">400</span><span class="pill">401</span><span class="pill">403</span><span class="pill">404</span>
          <span class="pill">409</span><span class="pill">415</span><span class="pill">422</span><span class="pill">429</span><span class="pill">500</span>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>2) Network Summary (Status + Headers) and Data Table</h2>
      <div class="body">
        <div class="kpi">
          <div class="box"><b>Last Status</b><span id="kStatus">-</span></div>
          <div class="box"><b>Location</b><span id="kLoc">-</span></div>
          <div class="box"><b>WWW-Authenticate</b><span id="kAuth">-</span></div>
          <div class="box"><b>Retry-After</b><span id="kRetry">-</span></div>
        </div>

        <div class="hint" style="margin-top:10px;">
          This panel is a summary only. For full details: <b>DevTools → Network</b> → click the request → Headers.
        </div>

        <h3 style="margin:12px 0 8px;font-size:13px;color:#334155;">People Table</h3>
        <div style="overflow:auto;border:1px solid var(--line);border-radius:14px;">
          <table id="tbl">
            <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Friends</th><th>Created</th><th>Updated</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const apiBase = "api.php";

function headersWithKey(extra = {}) {
  const k = document.getElementById('apiKey').value.trim();
  const h = {...extra};
  if (k) h["X-API-Key"] = k;
  return h;
}

function setKpis(resp) {
  document.getElementById('kStatus').textContent = resp.status;
  document.getElementById('kLoc').textContent = resp.headers.get('Location') || '-';
  document.getElementById('kAuth').textContent = resp.headers.get('WWW-Authenticate') || '-';
  document.getElementById('kRetry').textContent = resp.headers.get('Retry-After') || '-';
}

function toFriendsArray(csv) {
  return String(csv || "")
    .split(',')
    .map(s => s.trim())
    .filter(Boolean);
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function renderTable(rows) {
  const tb = document.querySelector('#tbl tbody');
  tb.innerHTML = "";
  for (const r of rows) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.id}</td>
      <td>${escapeHtml(r.name||"")}</td>
      <td>${escapeHtml(r.phone||"")}</td>
      <td>${Array.isArray(r.friends)? r.friends.map(escapeHtml).join(", ") : ""}</td>
      <td>${escapeHtml(r.created_at||"")}</td>
      <td>${escapeHtml(r.updated_at||"")}</td>
    `;
    tb.appendChild(tr);
  }
}

async function fetchListAndRender(updateKpi = true) {
  const resp = await fetch(`${apiBase}?action=list`, { headers: headersWithKey() });
  if (updateKpi) setKpis(resp);
  if (resp.status === 200) {
    const j = await resp.json();
    renderTable(j.data || []);
  }
}

function renderOp() {
  const op = document.getElementById('op').value;
  const ids = ["panelCreate","panelSearch","panelUpdate","panelDelete"];
  ids.forEach(id => document.getElementById(id).classList.add('hidden'));

  if (op === "create") document.getElementById('panelCreate').classList.remove('hidden');
  if (op === "search") document.getElementById('panelSearch').classList.remove('hidden');
  if (op === "update") document.getElementById('panelUpdate').classList.remove('hidden');
  if (op === "delete") document.getElementById('panelDelete').classList.remove('hidden');
}

async function submitOp() {
  const op = document.getElementById('op').value;

  if (op === "list") {
    await fetchListAndRender(false);
    return;
  }

  if (op === "create") {
    const body = {
      name: document.getElementById('cName').value,
      phone: document.getElementById('cPhone').value,
      friends: toFriendsArray(document.getElementById('cFriends').value)
    };
    const resp = await fetch(`${apiBase}?action=create`, {
      method: "POST",
      headers: headersWithKey({"Content-Type":"application/json"}),
      body: JSON.stringify(body)
    });
    setKpis(resp);
    await fetchListAndRender(false);
    return;
  }

  if (op === "search") {
    const q = document.getElementById('sQ').value;
    const resp = await fetch(`${apiBase}?action=search&q=${encodeURIComponent(q)}`, { headers: headersWithKey() });
    setKpis(resp);
    // search does not change table
    return;
  }

  if (op === "update") {
    const id = document.getElementById('uId').value.trim();
    const mode = document.getElementById('uMode').value;

    const body = {};
    const name = document.getElementById('uName').value.trim();
    const phone = document.getElementById('uPhone').value.trim();
    const friendsCsv = document.getElementById('uFriends').value.trim();

    if (name) body.name = name;
    if (phone) body.phone = phone;
    if (friendsCsv) body.friends = toFriendsArray(friendsCsv);

    // If PUT, require all fields at UI level for better UX
    if (mode === "PUT") {
      if (!body.name || !body.phone || !body.friends) {
        alert("For PUT, please provide Name, Phone, and Friends.");
        return;
      }
    }
    if (mode === "PATCH" && Object.keys(body).length === 0) {
      alert("For PATCH, provide at least one field to update.");
      return;
    }

    const resp = await fetch(`${apiBase}?id=${encodeURIComponent(id)}`, {
      method: mode,
      headers: headersWithKey({"Content-Type":"application/json"}),
      body: JSON.stringify(body)
    });
    setKpis(resp);
    await fetchListAndRender(false);
    return;
  }

  if (op === "delete") {
    const id = document.getElementById('dId').value.trim();
    const resp = await fetch(`${apiBase}?id=${encodeURIComponent(id)}`, {
      method: "DELETE",
      headers: headersWithKey()
    });
    setKpis(resp);
    await fetchListAndRender(false);
    return;
  }
}

renderOp();
fetchListAndRender(true);
</script>
</body>
</html>
