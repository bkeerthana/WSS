StampVault - Collection + JSON Log Viewer
======================================

What this app demonstrates
1) A simple stamp collection data entry form that stores items into stamps.json
2) Safe cookie collection + auto-send demo that stores logs into messages.jsonl
   - Only cookies visible to JavaScript are collected (document.cookie)
   - Common session cookie names are redacted before sending

Credentials
- Username: demo
- Password: demo123

How to run (XAMPP on Windows)
1) Copy the folder 'stampvault_collection_demo' to:
   C:\xampp\htdocs\stampvault_collection_demo\
2) Start Apache in XAMPP.
3) Open:
   http://localhost/stampvault_collection_demo/index.php
4) Login and go to Dashboard to add stamps and generate logs:
   http://localhost/stampvault_collection_demo/dashboard.php
5) View collected logs:
   http://localhost/stampvault_collection_demo/view.php

Files
- stamps.json      : your stamp collection items
- messages.jsonl   : auto-sent logs (one JSON object per line)
- receive.php      : receiver endpoint that appends to messages.jsonl


Session ID inclusion (safe)
- Dashboard and Cookie Demo show the raw server-side session id on the page for teaching.
- Logs store only session_id_hash (SHA-256), not the raw session id, to avoid enabling session hijacking.
