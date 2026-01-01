<?php
define('APP_NAME', 'PPT Weak Password Login Demo');

define('DB_HOST', 'localhost');
define('DB_NAME', 'ppt_weak_login_demo');
define('DB_USER', 'root');
define('DB_PASS', '');

define('TECH_ENFORCE_PASSWORD_POLICY', false); // OFF = weak passwords allowed
define('TECH_RATE_LIMIT', true);
define('TECH_LOCKOUT', true);

define('POLICY_MIN_PASSWORD_LEN', 10);
define('POLICY_REQUIRE_COMPLEXITY', true);
define('POLICY_MAX_FAILED_ATTEMPTS', 5);
define('POLICY_LOCKOUT_SECONDS', 120);
define('POLICY_RATE_LIMIT_MS', 800);
