<?php
function log_event($pdo,$action,$resource,$status,$details=null,$username_override=null){
 $username=$_SESSION['username']??$username_override;
 $stmt=$pdo->prepare("INSERT INTO audit_log(user_id,username,action,resource,ip,user_agent,status,details) VALUES (?,?,?,?,?,?,?,?)");
 $stmt->execute([$_SESSION['user_id']??null,$username,$action,$resource,$_SERVER['REMOTE_ADDR']??null,$_SERVER['HTTP_USER_AGENT']??null,$status,$details]);
}
?>