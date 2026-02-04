Attack Surface Lab (XAMPP) — Updated Realistic Code
==================================================

Install:
1) Copy folder "attack_surface_lab" into:
   C:\xampp\htdocs\wss\attack_surface_lab

2) Start Apache + MySQL in XAMPP.

3) Run setup once:
   http://localhost/wss/attack_surface_lab/setup.php

4) Open:
   http://localhost/wss/attack_surface_lab/index.php

Login flow:
- Login -> Dashboard -> My Profile / Feedback / Upload
- Admin/Staff have User Lookup page.

Demo credentials:
- anu / anu@123
- ruban / ruban@123
- keerthana / keerthana@123

API:
- curl -i http://localhost/wss/attack_surface_lab/api/status.php
- curl -i http://localhost/wss/attack_surface_lab/api/users.php -H "X-API-Key: lab123"

Register page:
- http://localhost/wss/attack_surface_lab/ui/register.php
