<?php
// Reflected XSS demo landing page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reflected XSS Lab</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .card { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        a { text-decoration: none; color: #0b57d0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Reflected XSS Teaching Lab</h1>

    <div class="card">
        <h2>1. Vulnerable Demo</h2>
        <p>This page reflects user input directly into HTML without sanitization.</p>
        <p><a href="vulnerable.php">Open vulnerable example</a></p>
    </div>

    <div class="card">
        <h2>2. Secure Demo</h2>
        <p>This page reflects user input safely using output encoding.</p>
        <p><a href="secure.php">Open secure example</a></p>
    </div>

    <div class="card">
        <h2>3. Payload Hints</h2>
        <p>Try inputs such as:</p>
        <ul>
            <li><code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></li>
            <li><code>&lt;b&gt;Hello&lt;/b&gt;</code></li>
            <li><code>&lt;img src=x onerror=alert('XSS')&gt;</code></li>
        </ul>
    </div>
</body>
</html>
