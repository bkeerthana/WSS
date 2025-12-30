<?php
require "config.php";
if(!isset($_SESSION['user_id'])){http_response_code(401);exit;}
$page=$_GET['page']??'unknown'; $sid=session_id();
$stmt=$pdo->prepare("INSERT INTO page_sessions(session_id,user_id,username,page,ip,user_agent) VALUES (?,?,?,?,?,?)");
$stmt->execute([$sid,$_SESSION['user_id'],$_SESSION['username'],$page,$_SERVER['REMOTE_ADDR']??null,substr($_SERVER['HTTP_USER_AGENT']??'',0,250)]);
echo $pdo->lastInsertId();
?>