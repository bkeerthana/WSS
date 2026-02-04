<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../auth.php";

$mode = lab_mode();

if ($mode === "VULNERABLE") {
  /**
   * VULNERABLE: Missing logout protection (teaching demo)
   * - Does NOT destroy session
   * - Only redirects to login
   * Result: user may still be authenticated if the session cookie remains.
   */
  header("Location: /wss/attack_surface_lab/ui/login.php?msg=Logged+out+(VULNERABLE:+session+not+destroyed)");
  exit;
}

/**
 * HARDENED: Proper logout
 */
logout_user();
header("Location: /wss/attack_surface_lab/ui/login.php?msg=Logged+out");
exit;
