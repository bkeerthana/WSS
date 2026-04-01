<?php
header('Content-Type: application/json');
echo json_encode([
    'message' => 'JWT logout is usually handled on the client side by deleting the token from storage.'
], JSON_PRETTY_PRINT);
?>