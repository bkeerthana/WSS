<?php
require_once __DIR__ . '/config.php';

function db_root(): PDO {
  return new PDO("mysql:host=".DB_HOST.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
}

function db_app(): PDO {
  return new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
}

function audit_log($actor,$event,$details=''){
  $pdo=db_app();
  $stmt=$pdo->prepare("INSERT INTO audit(actor,event,details) VALUES (?,?,?)");
  $stmt->execute([$actor,$event,$details]);
}

function password_policy_error($pw){
  if(!TECH_ENFORCE_PASSWORD_POLICY) return null;
  if(strlen($pw)<POLICY_MIN_PASSWORD_LEN) return "Password too short";
  if(POLICY_REQUIRE_COMPLEXITY){
    if(!preg_match('/[A-Z]/',$pw) || !preg_match('/[a-z]/',$pw) ||
       !preg_match('/[0-9]/',$pw) || !preg_match('/[^a-zA-Z0-9]/',$pw))
      return "Password lacks complexity";
  }
  return null;
}
