<?php
require "config.php"; require "log_event.php";
log_event($pdo,"LOGOUT","auth","SUCCESS");
session_destroy(); header("Location: login.php");
?>