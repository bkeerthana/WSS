McCumber Model Demo (PHP + MySQL)
================================

This folder contains a small “Student Marks Portal” to demonstrate:
- CIA (Confidentiality, Integrity, Availability)
- Data states (At Rest / In Transit / In Processing)
- Controls (People / Policy / Technology)

It supports two modes:
- SECURE_MODE = true  (hashing, prepared statements, session regeneration)
- SECURE_MODE = false (plain-text at rest, SQLi-prone query in username)

------------------------------------------------------------
A) Windows (XAMPP) INSTALLATION
------------------------------------------------------------

1) Install XAMPP
   - Download XAMPP for Windows from Apache Friends website.
   - Install with: Apache + MySQL + phpMyAdmin.

2) Copy the project folder
   - Copy the entire folder 'mccumber_demo' into:
     C:\xampp\htdocs\mccumber_demo\

3) Start services
   - Open XAMPP Control Panel
   - Start: Apache
   - Start: MySQL

4) Create database
   Option 1 (phpMyAdmin):
     - Open: http://localhost/phpmyadmin
     - Click “New”
     - Database name: mccumber_demo
     - Create

   Option 2 (MySQL CLI):
     CREATE DATABASE mccumber_demo;

5) Initialize tables + demo users
   - Open:
     http://localhost/mccumber_demo/init_db.php
   - You should see “Initialized OK”
   - Then click “Go to Login”

6) Login test
   - Open:
     http://localhost/mccumber_demo/login.php
   - Users:
     alice / 12345
     bob   / Bob@2026!

------------------------------------------------------------
B) Switch Secure/Insecure Demo
------------------------------------------------------------

Open config.php and set:
- define('SECURE_MODE', true);   // secure demo
or
- define('SECURE_MODE', false);  // insecure demo

After changing mode, open init_db.php again to re-seed users.

------------------------------------------------------------
C) What to Demonstrate for McCumber Cube
------------------------------------------------------------

1) Data at Rest:
   - SECURE_MODE=false shows plain-text passwords in DB (Confidentiality risk).
   - SECURE_MODE=true stores hashed passwords (Technology control).

2) Data in Transit:
   - Use browser DevTools > Network tab to show POST /process_login.php.
   - Explain why HTTPS/TLS is required (not included in localhost HTTP demo).

3) Data in Processing:
   - Show prepared statements vs unsafe query (SQL injection discussion).
   - Show lockout/rate limit (Availability control via Policy/Technology).

4) People + Policy:
   - policy.php page discusses password policy and user behavior.

------------------------------------------------------------
D) Common Errors
------------------------------------------------------------

- "Access denied for user 'root'@'localhost'"
  -> Update DB_USER / DB_PASS in config.php

- "Unknown database mccumber_demo"
  -> Create DB in phpMyAdmin first, then run init_db.php

- Page not found
  -> Ensure folder is in C:\xampp\htdocs\mccumber_demo\
  -> Ensure Apache is started

------------------------------------------------------------
E) Optional: Force HTTPS (Advanced)
------------------------------------------------------------

If you enable SSL in Apache and run https://localhost, you can additionally
demonstrate secure cookies. This is optional for class demo.


To sniff the packets: 
http.request.method == "POST"
