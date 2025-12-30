<?php
require_once __DIR__ . '/config.php';
session_name(SESSION_NAME);
session_start();

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
