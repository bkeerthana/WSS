<?php
// Update these for your XAMPP/WAMP MySQL
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mccumber_demo');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default is empty

// DEMO SWITCHES:
// true  => Secure mode (hashing, prepared statements, session hardening)
// false => Insecure mode (plain password, weak controls)
define('SECURE_MODE', true);

// Policy controls (Policy dimension)
define('POLICY_MIN_PASSWORD_LEN', 8);
define('POLICY_MAX_FAILED_ATTEMPTS', 5);
define('POLICY_LOCKOUT_SECONDS', 120);

// Technology controls (Technology dimension)
define('TECH_RATE_LIMIT_MS', 800); // delay per login attempt
?>
