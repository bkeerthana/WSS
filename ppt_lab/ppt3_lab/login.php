<?php
require_once __DIR__ . '/db.php';
session_start();
$err='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $u=$_POST['username']??'';
 $p=$_POST['password']??'';
 if(TECH_RATE_LIMIT) usleep(POLICY_RATE_LIMIT_MS*1000);
 $s=db_app()->prepare("SELECT * FROM users WHERE username=?");
 $s->execute([$u]);
 $r=$s->fetch(PDO::FETCH_ASSOC);
 if(!$r){$err='Invalid';}
 else{
  if(TECH_LOCKOUT && $r['locked_until'] && strtotime($r['locked_until'])>time()){
    $err='Locked';
  }elseif(password_verify($p,$r['password_hash'])){
    $_SESSION['u']=$u;
    audit_log($u,'LOGIN_OK','');
    header('Location: dashboard.php');exit;
  }else{
    $f=$r['failed_attempts']+1;
    $lu=null;
    if(TECH_LOCKOUT && $f>=POLICY_MAX_FAILED_ATTEMPTS)
      $lu=date('Y-m-d H:i:s',time()+POLICY_LOCKOUT_SECONDS);
    db_app()->prepare("UPDATE users SET failed_attempts=?,locked_until=? WHERE id=?")
      ->execute([$f,$lu,$r['id']]);
    audit_log($u,'LOGIN_FAIL',"fails=$f");
    $err='Invalid';
  }
 }
}
?>
<h2>Login</h2><?php echo $err;?>
<form method=post>
User <input name=username><br>
Pass <input name=password><br>
<button>Login</button>
</form>
<a href=audit.php>Audit</a>
