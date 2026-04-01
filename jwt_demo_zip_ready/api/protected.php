<?php
header('Content-Type: application/json');
require_once __DIR__ . '/lib/jwt_helper.php';

$secretKey = 'change_this_demo_secret_for_classroom';

$token = getBearerToken();

if (!$token) {
    http_response_code(401);
    echo json_encode([
        'message' => 'Authorization header missing or Bearer token not provided'
    ], JSON_PRETTY_PRINT);
    exit;
}

try {
    $payload = jwtDecode($token, $secretKey);

    echo json_encode([
        'message' => 'Access granted to protected resource',
        'user' => $payload['sub'] ?? null,
        'role' => $payload['role'] ?? null,
        'full_name' => $payload['full_name'] ?? null,
        'issued_at' => date('Y-m-d H:i:s', $payload['iat'] ?? time()),
        'expires_at' => date('Y-m-d H:i:s', $payload['exp'] ?? time()),
        'protected_data' => [
            'topic' => 'JWT Demo',
            'concepts' => [
                'authentication',
                'token verification',
                'expiry validation',
                'authorization header'
            ]
        ]
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'message' => 'Invalid or expired token',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>