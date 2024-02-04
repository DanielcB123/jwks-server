<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Load RSA keys
$privateKey = file_get_contents(__DIR__ . '/keys/private_key.pem');
$publicKey = file_get_contents(__DIR__ . '/keys/public_key.pem');

// Decode the public key to get its details for JWKS
$publicKeyResource = openssl_pkey_get_public($publicKey);
$keyDetails = openssl_pkey_get_details($publicKeyResource);

// Encode modulus and exponent for JWKS
$n = rtrim(strtr(base64_encode($keyDetails['rsa']['n']), '+/', '-_'), '=');
$e = rtrim(strtr(base64_encode($keyDetails['rsa']['e']), '+/', '-_'), '=');

// Simple request router
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

switch ($requestUri) {
    case '/.well-known/jwks.json':
        if ($method == 'GET') {
            // Serve the JWKS with dynamic public key
            $jwks = [
                'keys' => [
                    [
                        "kty" => "RSA",
                        "use" => "sig",
                        "kid" => "1",
                        "n" => $n,
                        "e" => $e,
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
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

            // Check for the 'expired' query parameter to issue an expired JWT
            if (isset($_GET['expired']) && $_GET['expired'] == 'true') {
                $expirationTime = $issuedAt - 3600; // Set expiration to 1 hour in the past
            }

            $payload = [
                'iss' => 'http://localhost:8080', // Issuer
                'aud' => 'http://localhost:8080', // Audience
                'iat' => $issuedAt, // Issued at
                'exp' => $expirationTime, // Expiration time
                'sub' => '1234567890', // Subject
                'name' => 'John Doe', // Example name
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256', '1'); // '1' is the key ID (kid)
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
