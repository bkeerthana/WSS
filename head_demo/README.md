# Stamp & Coin Collection Tracker (XAMPP / PHP) + HTTP HEAD Demo

This is a lightweight, file-backed PHP web app to manage a personal **stamp and coin collection** with **list, search, add, edit, delete** operations.

It also includes a clear, safe classroom demo for **HTTP HEAD**:
- `GET /api/items.php` returns JSON body + headers
- `HEAD /api/items.php` returns **headers only** (no body)
- `GET /api/export.php` returns CSV body + headers
- `HEAD /api/export.php` returns **headers only** (no body)

## 1) Install (Windows XAMPP)

1. Copy the folder `head_demo` into:
   - `C:\xampp\htdocs\`
2. Ensure Apache is running in XAMPP.
3. Open:
   - `http://localhost/head_demo/`

The app stores data in:
- `public/data/collection.json`

Logs are written to:
- `public/logs/app.log`

## 2) Features
- Add items (Stamp or Coin)
- Edit items
- Delete items
- Search by keyword
- Filters (Type, Country, Year)
- Export CSV

## 3) HEAD demo (recommended classroom flow)

### A) Compare GET vs HEAD (items list)

**GET** (headers + body):
```bash
curl -i "http://localhost/head_demo/api/items.php"
```

**HEAD** (headers only):
```bash
curl -I "http://localhost/head_demo/api/items.php"
```

Expected:
- Both responses include headers like `Content-Type`.
- GET includes JSON body.
- HEAD has **no body**.

### B) HEAD on Export CSV

**GET** (downloads CSV):
```bash
curl -i "http://localhost/head_demo/api/export.php?format=csv"
```

**HEAD** (metadata only):
```bash
curl -I "http://localhost/head_demo/api/export.php?format=csv"
```

About Head:
- HEAD is used for **metadata checks** (e.g., Content-Length) without downloading the report.
- Attackers can use HEAD for reconnaissance; therefore, you must **log and harden** headers.

## 4) Where to see logging

Open:
- `C:\xampp\apache\logs\access.log`  (server access log)
- `C:\xampp\htdocs\head_demo\logs\app.log` (app log)

The access log will show:
- `"HEAD /head_demo/api/items.php HTTP/1.1" ...`

## 5) Optional: Apache HEAD-only log (server-side)

Add to `C:\xampp\apache\conf\httpd.conf` (near CustomLog lines):

```apache
SetEnvIf Request_Method HEAD is_head
CustomLog "logs/head_requests.log" common env=is_head
```

Restart Apache.
Now:
- `C:\xampp\apache\logs\head_requests.log` will contain only HEAD requests.

## 6) Notes 
- This app supports RESTful methods via JavaScript Fetch:
  - `POST` create
  - `PUT` update
  - `DELETE` delete
  - `GET` list/search
  - `HEAD` headers-only demo
- If you want you can use  **OPTIONS / TRACE** later, you can extend the `api/` endpoints.
