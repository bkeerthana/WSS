REFLECTED XSS TEACHING LAB

Files included
--------------
1. index.php       - landing page
2. vulnerable.php  - vulnerable reflected XSS example
3. secure.php      - secure reflected XSS example
4. lab_exercises.txt - suggested exercises
5. payloads.txt    - sample payloads for classroom use

How to run in XAMPP
-------------------
1. Extract this folder.
2. Copy the folder into:
   C:\xampp\htdocs\
3. Start Apache in XAMPP Control Panel.
4. Open browser and visit:
   http://localhost/reflected_xss_lab/index.php

How to demonstrate
------------------
1. Open vulnerable.php
2. Enter normal text like:
   hello
3. Then enter:
   <script>alert('Reflected XSS')</script>
4. Show that the script executes because the server reflects the input back unsafely.
5. Open secure.php
6. Enter the same payload.
7. Show that it is displayed as text instead of executing.

Important note
--------------
This is for lab teaching in a controlled environment only.
