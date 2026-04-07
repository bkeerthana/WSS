<?php
require_once 'common.php';
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST['amount'];
    $to = $_POST['to'];

    $_SESSION['balance'] -= $amount;
    $message = "₹$amount transferred to $to";
}
?>

<h2>Transfer Money (Vulnerable)</h2>
<p><strong>Balance:</strong> ₹<?php echo $_SESSION['balance']; ?></p>

<?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>

<form method="POST">
    To: <input name="to"><br><br>
    Amount: <input name="amount"><br><br>
    <button>Transfer</button>
</form>

<a href="home.php">Back</a>
