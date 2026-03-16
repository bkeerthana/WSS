<?php
date_default_timezone_set("Asia/Kolkata");

echo "<h2>HTTP is Stateless Demo</h2>";
echo "<p>This page does not store anything in cookie or session.</p>";
echo "<p><b>Current Time:</b> " . date("Y-m-d H:i:s") . "</p>";
echo "<p><b>Request Method:</b> " . $_SERVER['REQUEST_METHOD'] . "</p>";

echo "<hr>";
echo "<p>Refresh the page multiple times.</p>";
echo "<p>The server responds each time, but it does not remember who you are.</p>";
echo "<p>No state is maintained across requests.</p>";


?>
<form method="POST" action="stateless.php">
    <button type="submit">Send POST</button>
</form>