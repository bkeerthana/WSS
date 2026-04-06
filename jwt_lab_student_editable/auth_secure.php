<?php
require_once 'config.php';
require_once 'jwt_helper.php';

function require_authentication_and_role($allowed_roles = []) {
    global $JWT_SECRET;

    $jwt = $_COOKIE['jwt_token'] ?? '';
    if (!$jwt) {
        die("Authentication required. <a href='index.php'>Login</a>");
    }

    // ==========================================================
    // LAB TASK 3: Comment out this check and observe what happens
    // when signature/algorithm verification is removed.
    // ==========================================================
    if (!verify_jwt_signature_and_alg($jwt, $JWT_SECRET, "HS256")) {
        die("Invalid token signature or algorithm. <a href='index.php'>Login</a>");
    }

    $payload = get_jwt_payload($jwt);
    if (!$payload) {
        die("Invalid token payload. <a href='index.php'>Login</a>");
    }

    // ==========================================================
    // LAB TASK 5: Comment out this expiry validation and test
    // whether expired tokens still work.
    // ==========================================================
    if (token_is_expired($payload)) {
        die("Token expired or missing exp. <a href='index.php'>Login</a>");
    }

    // ==========================================================
    // LAB TASK 2: Change allowed roles in dashboard files or
    // comment out this block to observe broken authorization.
    // ==========================================================
    if (!empty($allowed_roles) && !in_array($payload['role'] ?? '', $allowed_roles, true)) {
        die("Authorization failed for role <strong>" . htmlspecialchars($payload['role'] ?? 'unknown') . "</strong>. <a href='index.php'>Home</a>");
    }

    return $payload;
}
?>
