<?php
require_once 'common.php';
require_login();
?>

<h2>Welcome to Bank</h2>
<p><strong>Balance:</strong> ₹<?php echo $_SESSION['balance']; ?></p>

<ul>
    <li><a href="transfer_vuln.php">Transfer Money (Vulnerable)</a></li>
    <li><a href="transfer_secure.php">Transfer Money (Secure)</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
