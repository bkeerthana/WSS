<?php
ini_set('display_errors',1); error_reporting(E_ALL);
session_start();
$pdo=new PDO("mysql:host=localhost;dbname=aaa_lab;charset=utf8mb4","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
?>