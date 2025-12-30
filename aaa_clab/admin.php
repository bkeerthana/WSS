<?php
require "config.php"; require "log_event.php";
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}

// Toggle for lab:
// true  => vulnerable (students can access if they type URL)
// false => fixed RBAC (admin only)
define("VULNERABLE_MODE", false);

if(!VULNERABLE_MODE && $_SESSION['role']!="admin"){die("403 Forbidden");}
log_event($pdo,"VIEW","admin","SUCCESS");
?>
<!doctype html><html><head><title>Admin Panel</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>Admin Panel</h1><nav>Role: <span class="badge badge-admin">ADMIN</span></nav></header>
<div class="container">
  <?php if(VULNERABLE_MODE){ ?>
    <div class="alert alert-warn"><b>VULNERABLE MODE:</b> If a student can view this page, Authorization is broken.</div>
  <?php } else { ?>
    <div class="alert alert-ok"><b>FIXED MODE:</b> RBAC is enforced (admin-only).</div>
  <?php } ?>

  <div class="card">
    <h3>Administrative Controls</h3>
    <ul>
      <li>User management (demo)</li>
      <li>System settings (demo)</li>
      <li>Access control policies (demo)</li>
    </ul>
  </div>

  <a href="<?=($_SESSION['role']==='admin')?'admin_home.php':'student_home.php'?>" class="button">Back</a>
</div>
<div class="footer">AAA Security Lab – Admin Page</div>

<script>
(async function(){
 const r=await fetch("page_track_start.php?page=admin"); const id=await r.text();
 function end(){const d=new FormData(); d.append("id",id); navigator.sendBeacon("page_track_end.php",d);}
 window.addEventListener("beforeunload",end);
 document.addEventListener("visibilitychange",()=>{if(document.visibilityState==="hidden")end();});
})();
</script>
</body></html>