<?php
require "config.php"; require "log_event.php";
$error="";
if($_SERVER['REQUEST_METHOD']=="POST"){
 $u=trim($_POST['username']); $p=$_POST['password'];
 $s=$pdo->prepare("SELECT * FROM users WHERE username=? AND is_active=1");
 $s->execute([$u]); $r=$s->fetch();
 if($r && password_verify($p,$r['password_hash'])){
  $_SESSION['user_id']=$r['id']; $_SESSION['username']=$r['username']; $_SESSION['role']=$r['role'];
  log_event($pdo,"LOGIN","auth","SUCCESS","Role=".$r['role']);
  header("Location: ".($r['role']=="admin"?"admin_home.php":"student_home.php")); exit;
 } else { $error="Invalid credentials"; log_event($pdo,"LOGIN","auth","FAIL","Invalid ".$u,$u); }
}
?>
<!doctype html><html><head><title>AAA Login</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<header><h1>AAA Security Lab</h1><nav>Authentication • Authorization • Accountability</nav></header>
<div class="container">
  <div class="card">
    <h3>Login</h3>
    <?php if($error){ ?><div class="alert alert-warn"><?=htmlspecialchars($error)?></div><?php } ?>
    <form method="post">
      <div>Username</div>
      <input class="input" name="username" required><br><br>
      <div>Password</div>
      <input class="input" type="password" name="password" required><br><br>
      <button class="btn">Login</button>
    </form>
    <p><b>Demo:</b> student1/Student@123, student2/Student@123, student3/Student@123, admin/Admin@123</p>
  </div>
</div>
<div class="footer">AAA Lab Demo</div>
</body></html>