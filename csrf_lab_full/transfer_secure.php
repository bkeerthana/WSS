<?php
require_once 'common.php';
require_login();

$message = "";
$error = "";
$token = generate_csrf_token();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $submitted_token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($submitted_token)) {
        $error = "❌ CSRF Attack Blocked!";
    } else {
        $amount = $_POST['amount'];
        $to = $_POST['to'];

        $_SESSION['balance'] -= $amount;
        $message = "✅ ₹$amount transferred to $to";
    }
}
?>

<h2>Transfer Money (Secure)</h2>
<p><strong>Balance:</strong> ₹<?php echo $_SESSION['balance']; ?></p>

<?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    To: <input name="to"><br><br>
    Amount: <input name="amount"><br><br>

    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

    <button>Transfer</button>
</form>

<a href="home.php">Back</a>
