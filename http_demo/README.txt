HTTP GUI Demo (Fetch + PHP) - GET/POST/PUT/DELETE

Files:
- index.html : GUI that triggers requests using fetch()
- api.php    : PHP API endpoint handling methods
- data.json  : persistent storage
- README.txt : run instructions

Run on XAMPP (Windows):
1) Copy folder "http_demo" into: C:\xampp\htdocs\
2) Start Apache in XAMPP
3) Open: http://localhost/http_demo/index.html

Notes:
- POST creates a new user (name+role)
- PUT updates an existing user by id (name+role)
- DELETE removes an existing user by id
- GET shows all users or one user using ?id=
