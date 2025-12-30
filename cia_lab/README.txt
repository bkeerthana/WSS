1) Prerequisites

XAMPP installed
You can open:
XAMPP Control Panel
Browser


2) Create the Project Folder

Go to your XAMPP web root:

Windows: C:\xampp\htdocs\

Linux: /opt/lampp/htdocs/

Mac: /Applications/XAMPP/htdocs/

Create a folder:

cia_lab

Inside cia_lab, create these files:

config.php
db.php
init_db.sql
index.php
login.php
dashboard.php
profile.php
logout.php
seed_users.php   (temporary)


3) Start Apache and MySQL

Open XAMPP Control Panel

Click Start for:

Apache

MySQL

Make sure both show as “Running”.



4) Create the Database + Table (phpMyAdmin)

Open phpMyAdmin:

http://localhost/phpmyadmin/

Click Import

Choose file: init_db.sql

Click Go

This will create:

Database: cia_lab_db

Table: users

Quick check

In phpMyAdmin:

Left panel → click cia_lab_db

Confirm table users exists


5) Seed Demo Users (Run Once)

This step inserts 3 demo users: alice, bob, admin.

In browser open:

http://localhost/cia_lab/seed_users.php

You should see:

“Seeded users successfully. Now DELETE seed_users.php for safety.”

Verify users inserted

In phpMyAdmin:

cia_lab_db → users → Browse

You should see 3 rows.


ERROR:




6) Delete seed_users.php (Important)

Delete the file:

C:\xampp\htdocs\cia_lab\seed_users.php

Reason: it can create accounts again if someone accesses it.


7) Run the Web App

Open:

http://localhost/cia_lab/

Click Login and use:

alice / Alice@123

bob / Bob@123

admin / Admin@123



Possible Errors: 

8) If Login Fails (Common Fixes)
A) Database connection error

Check config.php values:

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'cia_lab_db');
define('DB_USER', 'root');
define('DB_PASS', '');


On some XAMPP setups, DB host works with localhost too.

If you set a MySQL root password in XAMPP, update DB_PASS.

B) Users not inserted

If you forgot to run seed:

Run http://localhost/cia_lab/seed_users.php again (only if it still exists)

Or re-import SQL + re-run seed

C) Port issues

Apache default is 80

If busy, XAMPP may run on 8080:

Then use: http://localhost:8080/cia_lab/


Another error:
Fatal error: Uncaught PDOException: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'alice' for key 'username' in C:\xampp\htdocs\wss\4\cia_lab\seed_users.php:15 Stack trace: #0 C:\xampp\htdocs\wss\4\cia_lab\seed_users.php(15): PDOStatement->execute(Array) #1 {main} thrown in C:\xampp\htdocs\wss\4\cia_lab\seed_users.php on line 15

Empty the users table, then seed once

Use this if you want a fresh database.

Open phpMyAdmin:

http://localhost/phpmyadmin/

Select database: cia_lab_db

Open table: users

Click Empty → confirm TRUNCATE

Now open (only once):

http://localhost/wss/4/cia_lab/seed_users.php

When seeding succeeds, delete seed_users.php.

C) Delete seeder

Delete:

C:\xampp\htdocs\wss\4\cia_lab\seed_users.php

D) Login

Open:

http://localhost/wss/4/cia_lab/login.php

Use:

alice / Alice@123

bob / Bob@123

admin / Admin@123

If you follow this reset flow, “Invalid password” should disappear.

To add account_balance: 
MySQL -u root 
show databases; 
use database; 
ALTER TABLE users ADD COLUMN account_balance DECIMAL(10,2) NOT NULL DEFAULT 10000.00;

UPDATE users SET account_balance = 25000.00 WHERE username = 'alice';
UPDATE users SET account_balance = 18000.00 WHERE username = 'bob';
UPDATE users SET account_balance = 999999.00 WHERE username = 'admin';