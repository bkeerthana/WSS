# Interactive CRUD + HTTP Response Codes Lab (PHP/XAMPP)

This is a user-friendly CRUD web app where students select an operation from a dropdown (instead of many buttons).
The goal is to **observe HTTP status codes and headers** in DevTools -> Network.

## Run in XAMPP
1. Start Apache in XAMPP.
2. Copy folder `status_demo` into:
   C:\xampp\htdocs\status_demo
3. Open:
   http://localhost/status_demo/public/

## How students learn
- The UI shows only:
  - last status code
  - Location / WWW-Authenticate / Retry-After headers (if present)
- The response body is not shown in UI. 
  DevTools -> Network

## Expected status codes by operation
- List: 200
- Create: 201 (success), 422 (validation), 409 (duplicate phone), 
  ## 415 (wrong content-type) demo using postman
- Search: 200 (match), 404 (no match, teaching mode - no body), 400 (missing q)
- Update: 200 (success), 404 (id not found), 422 (validation), 409 (duplicate phone), 415 (wrong content-type)
- Delete: 204 (success), 404 (id not found)

## Reset
Delete `data/people.json` and `data/meta.json` and re-copy from the zip to reset.


