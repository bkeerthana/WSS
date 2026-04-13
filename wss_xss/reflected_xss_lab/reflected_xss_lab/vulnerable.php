<?php
$q = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Reflected XSS Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .box { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        input[type=text] { width: 350px; padding: 8px; }
        button { padding: 8px 12px; }
        .result { margin-top: 20px; padding: 12px; background: #fff4f4; border-left: 4px solid #d33; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Vulnerable Reflected XSS Example</h1>
    <div class="box">
        <form method="GET" action="">
            <label>Search term:</label><br><br>
            <input type="text" name="q" value="<?php echo $q; ?>">
            <button type="submit">Search</button>
        </form>

        <div class="result">
            <strong>Search result for:</strong>
            <?php echo $q; ?>
        </div>

        <p><strong>Why vulnerable?</strong> The value from <code>q</code> is printed directly into the page without encoding.</p>
        <p><strong>Try:</strong> <code>&lt;script&gt;alert('Reflected XSS')&lt;/script&gt;</code></p>
        <p><a href="index.php">Back</a></p>
    </div>
</body>
</html>
