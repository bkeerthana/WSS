DOM-BASED XSS TEACHING LAB

Files included
--------------
1. index.html       - landing page
2. vulnerable.html  - vulnerable DOM-based XSS example
3. secure.html      - secure DOM-based XSS example
4. lab_exercises.txt - classroom exercises
5. payloads.txt     - sample payloads

How to run
----------
Option 1: Open index.html directly in a browser.

Option 2: Place the folder inside:
C:\xampp\htdocs\dom_xss_lab\
Then open:
http://localhost/dom_xss_lab/index.html

How to demonstrate
------------------
1. Open vulnerable.html
2. Type normal input such as:
   Hello Student
3. Observe that it is displayed.
4. Then type:
   <img src=x onerror=alert('DOM XSS')>
5. Click Update.
6. Show that JavaScript executes.

Alternative direct URL
----------------------
Open:
vulnerable.html#<img src=x onerror=alert('DOM XSS')>

Why this is DOM XSS
-------------------
The payload is handled by client-side JavaScript.
The server does not reflect the input.
The issue happens because JavaScript writes untrusted data into the DOM using innerHTML.
