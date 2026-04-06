<?php
// ==========================================
// JWT Lab - Configuration
// ==========================================

// SECURE DEFAULT:
// Keep a stronger secret first, then ask students to change it to "12345"
// to observe the weak-secret-key problem.
$JWT_SECRET = "Strong_Lab_Secret_2026_#JWT";

// Demo users (plain text for lab simplicity only)
$USERS = [
    "student1" => ["password" => "student123", "role" => "student", "name" => "Student One"],
    "faculty1" => ["password" => "faculty123", "role" => "faculty", "name" => "Faculty One"],
    "admin1"   => ["password" => "admin123",   "role" => "admin",   "name" => "Admin One"]
];
?>
