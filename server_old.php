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

// Request router
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($requestUri, PHP_URL_PATH); // Extract the path from the request URI

header('Content-Type: application/json');

switch ($path) { // Using the extracted path for routing
    case '/.well-known/jwks.json':
        if ($method == 'GET') {
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
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;
    
    case '/auth':
        if ($method == 'POST') {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

            // Check for the 'expired' query parameter to issue an expired JWT
            if (isset($_GET['expired']) && $_GET['expired'] === 'true') {
                $expirationTime = $issuedAt - 3600; // Setting exp to 1 hour in the past
            }

            $payload = [
                'iss' => 'http://127.0.0.1:8080',
                'aud' => 'http://127.0.0.1:8080',
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'sub' => '1234567890',
                'name' => 'John Doe',
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256', '1');
            http_response_code(200);
            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;
    
    default:
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
