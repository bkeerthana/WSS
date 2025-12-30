<?php
require "config.php";
if(!isset($_SESSION['user_id'])){http_response_code(401);exit;}
$id=$_POST['id']??''; $sid=session_id();
$stmt=$pdo->prepare("UPDATE page_sessions SET ended_at=NOW(),duration_seconds=TIMESTAMPDIFF(SECOND,started_at,NOW()) WHERE id=? AND session_id=? AND ended_at IS NULL");
$stmt->execute([$id,$sid]);
?>