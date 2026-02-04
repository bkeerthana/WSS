<?php
require_once __DIR__ . "/../config.php";
header("Content-Type: application/json; charset=utf-8");
echo json_encode(["ok"=>true,"service"=>"attack_surface_lab","mode"=>lab_mode(),"time"=>date("c")], JSON_PRETTY_PRINT);
