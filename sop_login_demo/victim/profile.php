<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

header("Content-Type: application/json");
if (!isset($_SESSION['user'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}
$data = [
    "username" => $_SESSION['user'],
    "email" => "keerthana@example.com",
    "balance" => 5000
];
echo json_encode($data);
?>