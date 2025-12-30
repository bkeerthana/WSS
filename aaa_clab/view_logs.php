<?php
require "config.php"; require "log_event.php";
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}

// Toggle for lab:
// true  => vulnerable (students can view logs by typing URL)
// false => fixed RBAC (admin only)
define("VULNERABLE_MODE", true);

if(!VULNERABLE_MODE && $_SESSION['role']!="admin"){die("403 Forbidden");}
$r=$pdo->query("SELECT * FROM audit_log ORDER BY id DESC LIMIT 300")->fetchAll();
?>
<!doctype html><html><head><title>Audit Logs</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>Audit Logs</h1><nav>Accountability • Sensitive Data</nav></header>
<div class="container">
  <?php if(VULNERABLE_MODE){ ?>
    <div class="alert alert-warn"><b>VULNERABLE MODE:</b> Logs are exposed to students (security failure).</div>
  <?php } else { ?>
    <div class="alert alert-ok"><b>FIXED MODE:</b> Logs are restricted to admin only.</div>
  <?php } ?>

  <div class="card">
    <h3>Audit Trail</h3>
    <table>
      <tr><th>Time</th><th>User</th><th>Action</th><th>Resource</th><th>Status</th><th>Details</th></tr>
      <?php foreach($r as $row){ ?>
        <tr>
          <td><?=htmlspecialchars($row['ts']??'')?></td>
          <td><?=htmlspecialchars($row['username']??'')?></td>
          <td><?=htmlspecialchars($row['action']??'')?></td>
          <td><?=htmlspecialchars($row['resource']??'')?></td>
          <td><?=htmlspecialchars($row['status']??'')?></td>
          <td><?=htmlspecialchars($row['details']??'')?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <a href="<?=($_SESSION['role']==='admin')?'admin_home.php':'student_home.php'?>" class="button">Back</a>
</div>
<div class="footer">AAA Security Lab – Logs</div>

<script>
(async function(){
 const r=await fetch("page_track_start.php?page=logs"); const id=await r.text();
 function end(){const d=new FormData(); d.append("id",id); navigator.sendBeacon("page_track_end.php",d);}
 window.addEventListener("beforeunload",end);
 document.addEventListener("visibilitychange",()=>{if(document.visibilityState==="hidden")end();});
})();
</script>
</body></html>