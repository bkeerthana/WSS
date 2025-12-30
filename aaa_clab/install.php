<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
$host="localhost"; $user="root"; $pass=""; $db="aaa_lab";
try{
 $pdo=new PDO("mysql:host=$host;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`; USE `$db`;");
 $pdo->exec("DROP TABLE IF EXISTS audit_log; DROP TABLE IF EXISTS users; DROP TABLE IF EXISTS page_sessions;");
 $pdo->exec("CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50) UNIQUE,password_hash VARCHAR(255),role ENUM('student','admin'),is_active TINYINT(1) DEFAULT 1)");
 $pdo->exec("CREATE TABLE audit_log(id INT AUTO_INCREMENT PRIMARY KEY,ts DATETIME DEFAULT CURRENT_TIMESTAMP,user_id INT,username VARCHAR(50),action VARCHAR(100),resource VARCHAR(100),ip VARCHAR(45),user_agent VARCHAR(255),status VARCHAR(20),details VARCHAR(255))");
 $pdo->exec("CREATE TABLE page_sessions(id INT AUTO_INCREMENT PRIMARY KEY,session_id VARCHAR(128),user_id INT,username VARCHAR(50),page VARCHAR(120),started_at DATETIME DEFAULT CURRENT_TIMESTAMP,ended_at DATETIME NULL,duration_seconds INT NULL,ip VARCHAR(45),user_agent VARCHAR(255))");
 $stmt=$pdo->prepare("INSERT INTO users(username,password_hash,role) VALUES (?,?,?)");
 $stmt->execute(["student1",password_hash("Student@123",PASSWORD_DEFAULT),"student"]);
 $stmt->execute(["student2",password_hash("Student@123",PASSWORD_DEFAULT),"student"]);
 $stmt->execute(["student3",password_hash("Student@123",PASSWORD_DEFAULT),"student"]);
 $stmt->execute(["admin",password_hash("Admin@123",PASSWORD_DEFAULT),"admin"]);
 echo "<h2>Installed</h2><p>Delete install.php and open <a href='login.php'>login.php</a></p>";
}catch(Exception $e){ echo $e->getMessage(); }
?>