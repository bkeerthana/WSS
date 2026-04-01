# PHP JWT Hands-on Demo (Zip Ready)

This project is a simple JWT demo for classroom use with XAMPP and PHP.

## Features
- Login with username and password
- JWT generation without Composer
- Protected API endpoint
- Token expiry handling
- Logout by removing token from browser storage
- Simple browser interface
- cURL examples for testing

## Default login
- Username: `student`
- Password: `12345`

## How to run in XAMPP
1. Extract this folder into:
   `C:\xampp\htdocs\`
2. Start Apache in XAMPP.
3. Open:
   `http://localhost/jwt_demo_zip_ready/`

## Project structure
- `index.html` - login UI and protected resource access
- `api/login.php` - validates credentials and returns JWT
- `api/protected.php` - verifies JWT and returns protected data
- `api/logout.php` - explains logout
- `api/lib/jwt_helper.php` - manual JWT encode/decode helper

## Important notes
- This is for teaching and demonstration.
- In production, use HTTPS, stronger secrets, secure storage, refresh tokens, and proper user/database handling.
- JWT payload is encoded, not encrypted. Do not place secrets inside the payload.

## cURL examples

### Login
Windows CMD:
curl -X POST http://localhost/jwt_demo_zip_ready/api/login.php ^
-H "Content-Type: application/json" ^
-d "{\"username\":\"student\",\"password\":\"12345\"}"

Linux/macOS:
curl -X POST http://localhost/jwt_demo_zip_ready/api/login.php \
-H "Content-Type: application/json" \
-d '{"username":"student","password":"12345"}'

### Protected endpoint
curl -X GET http://localhost/jwt_demo_zip_ready/api/protected.php ^
-H "Authorization: Bearer YOUR_TOKEN_HERE"

## Suggested classroom activities
1. Login with correct and wrong credentials
2. Copy token and decode it at a JWT debugger website
3. Change one character in token and observe verification failure
4. Wait for token expiry and try again
5. Discuss stateless authentication vs PHP sessions
