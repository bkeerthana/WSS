<?php
require_once __DIR__ . "/../auth.php";
require_login();

header("Content-Type: text/plain; charset=UTF-8");

$info = session_debug_info();
echo "MODE=" . $info['mode'] . "\n";
echo "PRE_LOGIN_SID=" . ($info['pre_login_sid'] ?? "n/a") . "\n";
echo "POST_LOGIN_SID=" . ($info['post_login_sid'] ?? "n/a") . "\n";
echo "CURRENT_SID=" . $info['current_sid'] . "\n";
echo "USERNAME=" . (current_user()['username'] ?? "n/a") . "\n";
