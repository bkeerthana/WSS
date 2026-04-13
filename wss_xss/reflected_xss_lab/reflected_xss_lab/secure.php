<?php
$q = $_GET['q'] ?? '';
$safe_q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Reflected XSS Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .box { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        input[type=text] { width: 350px; padding: 8px; }
        button { padding: 8px 12px; }
        .result { margin-top: 20px; padding: 12px; background: #f4fff4; border-left: 4px solid #2d7; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Secure Reflected XSS Example</h1>
    <div class="box">
        <form method="GET" action="">
            <label>Search term:</label><br><br>
            <input type="text" name="q" value="<?php echo $safe_q; ?>">
            <button type="submit">Search</button>
        </form>

        <div class="result">
            <strong>Search result for:</strong>
            <?php echo $safe_q; ?>
        </div>

        <p><strong>Why secure?</strong> User input is encoded using <code>htmlspecialchars()</code>.</p>
        <p><strong>Try the same payload:</strong> <code>&lt;script&gt;alert('Reflected XSS')&lt;/script&gt;</code></p>
        <p><a href="index.php">Back</a></p>
    </div>
</body>
</html>
