<?php
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbPath = __DIR__ . '/data/comments.db';

    try {
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            comment TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);

        $message = "Database created successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Install DB</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Install Database</h1>

<?php if ($message): ?>
<div class="msg"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="post">
<button type="submit">Install DB</button>
</form>

<p><a href="index.php">Go to Lab</a></p>
</div>
</body>
</html>
