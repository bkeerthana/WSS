<?php
function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwtEncode(array $payload, string $secret, string $alg = 'HS256'): string {
    $header = [
        'typ' => 'JWT',
        'alg' => $alg
    ];

    $headerEncoded = base64UrlEncode(json_encode($header));
    $payloadEncoded = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true);
    $signatureEncoded = base64UrlEncode($signature);

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

function jwtDecode(string $jwt, string $secret): array {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception('Invalid token format');
    }

    list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

    $header = json_decode(base64UrlDecode($headerEncoded), true);
    $payload = json_decode(base64UrlDecode($payloadEncoded), true);

    if (!$header || !$payload) {
        throw new Exception('Invalid token encoding');
    }

    if (($header['alg'] ?? '') !== 'HS256') {
        throw new Exception('Unsupported algorithm');
    }

    $expectedSignature = base64UrlEncode(
        hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true)
    );

    if (!hash_equals($expectedSignature, $signatureEncoded)) {
        throw new Exception('Invalid signature');
    }

    if (isset($payload['nbf']) && time() < $payload['nbf']) {
        throw new Exception('Token not yet valid');
    }

    if (isset($payload['exp']) && time() >= $payload['exp']) {
        throw new Exception('Token expired');
    }

    return $payload;
}

function getBearerToken(): ?string {
    $header = null;

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['Authorization'])) {
        $header = $_SERVER['Authorization'];
    } elseif (function_exists('getallheaders')) {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                $header = $value;
                break;
            }
        }
    }

    if (!$header) {
        return null;
    }

    if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
        return trim($matches[1]);
    }

    return null;
}
?>