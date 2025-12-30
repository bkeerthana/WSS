<?php
require "config.php"; require "log_event.php";
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
if($_SESSION['role']!="admin"){die("403 Forbidden");}
log_event($pdo,"VIEW","admin_home","SUCCESS");
?>
<!doctype html><html><head><title>Admin Home</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>Admin Dashboard</h1><nav>Role: <span class="badge badge-admin">ADMIN</span></nav></header>
<div class="container">
  <div class="card">
    <h3>System Overview</h3>
    <p>Manage administration pages and monitor accountability data.</p>
  </div>
  <div class="card">
    <h3>Admin Actions</h3>
    <a class="button" href="admin.php">Admin Panel</a>
    <a class="button button-danger" href="view_logs.php">Audit Logs</a>
    <a class="button" href="page_report.php">Page Report</a>
  </div>
  <a href="logout.php" class="button button-muted">Logout</a>
</div>
<div class="footer">AAA Security Lab – Admin View</div>

<script>
(async function(){
 const r=await fetch("page_track_start.php?page=admin_home"); const id=await r.text();
 function end(){const d=new FormData(); d.append("id",id); navigator.sendBeacon("page_track_end.php",d);}
 window.addEventListener("beforeunload",end);
 document.addEventListener("visibilitychange",()=>{if(document.visibilityState==="hidden")end();});
})();
</script>
</body></html>