<?php
session_start();
$_SESSION = [];
session_destroy();
echo "<h2>Logged out</h2>";
?>