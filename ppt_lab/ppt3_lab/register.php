<?php
require_once __DIR__ . '/db.php';
$msg=$err='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $u=$_POST['username']??'';
 $p=$_POST['password']??'';
 if($e=password_policy_error($p)) $err=$e;
 else{
  $h=password_hash($p,PASSWORD_DEFAULT);
  try{
   db_app()->prepare("INSERT INTO users(username,password_hash) VALUES (?,?)")
           ->execute([$u,$h]);
   audit_log($u,'REGISTER','policy='. (TECH_ENFORCE_PASSWORD_POLICY?'on':'off'));
   $msg='Registered';
  }catch(Exception $x){$err='User exists';}
 }
}
?>
<h2>Register</h2>
<p>Policy: <?php echo TECH_ENFORCE_PASSWORD_POLICY?'ON':'OFF (weak allowed)';?></p>
<?php echo $msg."<br>".$err;?>
<form method=post>
User <input name=username><br>
Pass <input name=password><br>
<button>Register</button>
</form>
<a href=login.php>Login</a>
