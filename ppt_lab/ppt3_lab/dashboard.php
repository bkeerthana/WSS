<?php
session_start();
if(!isset($_SESSION['u'])){header('Location: login.php');exit;}
?>
<h2>Dashboard</h2>
Welcome <?php echo $_SESSION['u'];?><br>
<a href=audit.php>Audit</a> | <a href=logout.php>Logout</a>
