<?php
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function create_jwt($payload, $secret, $alg = "HS256") {
    $header = [
        "alg" => $alg,
        "typ" => "JWT"
    ];

    $header_encoded = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));
    $data = $header_encoded . "." . $payload_encoded;

    if ($alg === "HS256") {
        $signature = hash_hmac('sha256', $data, $secret, true);
        $signature_encoded = base64url_encode($signature);
        return $data . "." . $signature_encoded;
    }

    if ($alg === "none") {
        // LAB NOTE:
        // This is intentionally added only to demonstrate why alg=none is dangerous
        // if the server trusts the token header blindly.
        return $data . ".";
    }

    return null;
}

function get_jwt_parts($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null;
    }
    return $parts;
}

function get_jwt_header($jwt) {
    $parts = get_jwt_parts($jwt);
    if (!$parts) return null;
    return json_decode(base64url_decode($parts[0]), true);
}

function get_jwt_payload($jwt) {
    $parts = get_jwt_parts($jwt);
    if (!$parts) return null;
    return json_decode(base64url_decode($parts[1]), true);
}

function verify_jwt_signature($jwt, $secret) {
    $parts = get_jwt_parts($jwt);
    if (!$parts) return false;

    [$header, $payload, $signature] = $parts;
    $data = $header . "." . $payload;

    $expected_signature = base64url_encode(
        hash_hmac('sha256', $data, $secret, true)
    );

    return hash_equals($expected_signature, $signature);
}

function verify_jwt_signature_and_alg($jwt, $secret, $allowed_alg = "HS256") {
    $header = get_jwt_header($jwt);
    if (!$header) return false;

    if (($header['alg'] ?? '') !== $allowed_alg) {
        return false;
    }

    if ($allowed_alg === "HS256") {
        return verify_jwt_signature($jwt, $secret);
    }

    return false;
}

function token_is_expired($payload) {
    if (!is_array($payload)) return true;
    if (!isset($payload['exp'])) return true;
    return $payload['exp'] < time();
}
?>
