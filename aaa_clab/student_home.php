<?php
require "config.php"; require "log_event.php";
if(!isset($_SESSION['user_id'])){header("Location: login.php");exit;}
if($_SESSION['role']!="student"){die("403 Forbidden");}
log_event($pdo,"VIEW","student_home","SUCCESS");
?>
<!doctype html><html><head><title>Student Home</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>Student Portal</h1><nav>Role: <span class="badge badge-student">STUDENT</span></nav></header>
<div class="container">
  <div class="card">
    <h3>Welcome, <?=htmlspecialchars($_SESSION['username'])?></h3>
    <p>Your student dashboard is active.</p>
  </div>

  <div class="card">
    <h3>My Dashboard</h3>
    <ul>
      <li>Profile Information</li>
      <li>Assignments</li>
      <li>Attendance</li>
    </ul>
  </div>

  <div class="card">
    <h3>Security Lab Task</h3>
    <div class="alert alert-warn"><b>Instruction:</b> Try accessing restricted pages by modifying the URL in the browser address bar.</div>
    <p>Target pages (do not click; type in address bar): <code>/admin.php</code> and <code>/view_logs.php</code></p>
  </div>

  <a href="logout.php" class="button button-muted">Logout</a>
</div>
<div class="footer">AAA Security Lab – Student View</div>

<script>
(async function(){
 const r=await fetch("page_track_start.php?page=student_home"); const id=await r.text();
 function end(){const d=new FormData(); d.append("id",id); navigator.sendBeacon("page_track_end.php",d);}
 window.addEventListener("beforeunload",end);
 document.addEventListener("visibilitychange",()=>{if(document.visibilityState==="hidden")end();});
})();
</script>
</body></html>