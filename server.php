<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Function to get the bearer token from the request header
function getBearerToken() {
    $headers = apache_request_headers();
    if (!empty($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Load RSA keys
$privateKey = file_get_contents(__DIR__ . '/keys/private_key.pem');
$publicKey = file_get_contents(__DIR__ . '/keys/public_key.pem');

// Simple request router
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

switch ($requestUri) {
    case '/.well-known/jwks.json':
        if ($method == 'GET') {
            // Serve the JWKS
            $jwks = [
                'keys' => [
                    // Example JWK (You should generate this based on your public key)
                    [
                        "kty" => "RSA",
                        "use" => "sig",
                        "kid" => "1",
                        "n" => "public_key_part_n",
                        "e" => "AQAB",
                    ],
                ],
            ];
            echo json_encode($jwks);
        } else {
            http_response_code(405); // Method Not Allowed
        }
        break;
    
    case '/auth':
        if ($method == 'POST') {
            // Issue a JWT
            $payload = [
                'iss' => 'http://localhost:8080', // Issuer
                'aud' => 'http://localhost:8080', // Audience
                'iat' => time(), // Issued at: time when the token was generated
                'exp' => time() + 3600, // Expiration time
                'sub' => '1234567890', // Subject
                'name' => 'John Doe', // Example name
            ];
            
            $jwt = JWT::encode($payload, $privateKey, 'RS256');
            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(405); // Method Not Allowed
        }
        break;
    
    default:
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
