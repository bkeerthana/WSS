<?php
header('Content-Type: application/json');
require_once __DIR__ . '/lib/jwt_helper.php';

$secretKey = 'change_this_demo_secret_for_classroom';
$issuer = 'http://localhost';
$audience = 'jwt_demo_students';

$validUsers = [
    'student' => [
        'password' => '12345',
        'role' => 'student',
        'full_name' => 'Demo Student'
    ],
    'admin' => [
        'password' => 'admin123',
        'role' => 'admin',
        'full_name' => 'Demo Admin'
    ]
];

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON input']);
    exit;
}

$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Username and password are required']);
    exit;
}

if (!isset($validUsers[$username]) || $validUsers[$username]['password'] !== $password) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid credentials']);
    exit;
}

$issuedAt = time();
$expiresAt = $issuedAt + 300;

$payload = [
    'iss' => $issuer,
    'aud' => $audience,
    'iat' => $issuedAt,
    'nbf' => $issuedAt,
    'exp' => $expiresAt,
    'sub' => $username,
    'role' => $validUsers[$username]['role'],
    'full_name' => $validUsers[$username]['full_name']
];

$token = jwtEncode($payload, $secretKey);

echo json_encode([
    'message' => 'Login successful',
    'token' => $token,
    'expires_in_seconds' => 300,
    'token_type' => 'Bearer',
    'note' => 'Copy this token and use it in the Authorization header as: Bearer <token>'
], JSON_PRETTY_PRINT);
?>