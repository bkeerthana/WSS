# AAA Styled Demo (PHP + MySQL)

## Users
- student1 / Student@123
- student2 / Student@123
- student3 / Student@123
- admin / Admin@123

## Setup
1. Copy folder to `C:\xampp\htdocs\aaa_lab\`
2. Start Apache + MySQL (XAMPP)
3. Run `http://localhost/aaa_lab/install.php` once
4. Delete `install.php`
5. Open `http://localhost/aaa_lab/login.php`

## Vulnerable vs Fixed
Edit:
- `admin.php` -> define("VULNERABLE_MODE", true/false);
- `view_logs.php` -> define("VULNERABLE_MODE", true/false);

## Page tracking
- Page visit + duration stored in `page_sessions`
- Admin report: `page_report.php`
