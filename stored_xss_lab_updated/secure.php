<?php
require 'db.php';

$message = "";

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $stmt->execute([':id'=>$id]);
    $message = "Comment deleted.";
}

if (isset($_POST['delete_all'])) {
    $pdo->exec("DELETE FROM comments");
    $message = "All comments deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_all'])) {
    $u = $_POST['username'] ?? '';
    $c = $_POST['comment'] ?? '';

    if ($u && $c) {
        $stmt = $pdo->prepare("INSERT INTO comments (username, comment) VALUES (:u,:c)");
        $stmt->execute([':u'=>$u, ':c'=>$c]);
        $message = "Comment added.";
    }
}

$rows = $pdo->query("SELECT * FROM comments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Stored XSS - Secure</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

<h1>Stored XSS (Secure)</h1>

<p>
<a href="install_db.php">Install DB</a> |
<a href="index.php">Vulnerable Page</a>
</p>

<?php if ($message): ?>
<div class="msg"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="post">
<input name="username" placeholder="Name" required>
<textarea name="comment" placeholder="Comment" required></textarea>
<button>Post</button>
</form>

<form method="post">
<button name="delete_all">Delete All</button>
</form>

<hr>

<?php foreach($rows as $r): ?>
<div class="comment-box">
<strong><?php echo htmlspecialchars($r['username']); ?></strong>
<p><?php echo htmlspecialchars($r['comment']); ?></p>
<a href="?delete_id=<?php echo $r['id']; ?>">Delete</a>
</div>
<?php endforeach; ?>

</div>
</body>
</html>
