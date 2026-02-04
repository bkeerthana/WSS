<?php
//require_once __DIR__ . "/../config.php";

echo "<h3>Config-Triggered Logic Bomb (Safe)</h3>";

//if (lab_mode() === "VULNERABLE") {
  echo "<p style='color:orange; font-size:20px;'><b>Hi 👋</b></p>";
  echo "<p>Logic bomb triggered because mode is VULNERABLE.</p>";
//} else {
  echo "<p>HARDENED mode. No logic bomb.</p>";
}
