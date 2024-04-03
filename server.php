<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Prepare database connection
$dbFile = 'totally_not_my_privateKeys.db';
$pdo = new PDO('sqlite:' . $dbFile);

// AES encryption/decryption key
$encryptionKey = getenv('NOT_MY_KEY'); // Ensure this environment variable is securely set

// Request router
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($requestUri, PHP_URL_PATH); // Extract the path from the request URI

header('Content-Type: application/json');

// Simple rate limiter setup
$rateLimitWindow = 10; // seconds
$rateLimitMaxRequests = 10;
$rateLimiterStorage = [];

switch ($path) {
    case '/.well-known/jwks.json':
        if ($method == 'GET') {
            // Fetch all valid keys
            $currentTime = time();
            $sql = "SELECT key FROM keys WHERE exp >= :now";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':now' => $currentTime]);
            $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $jwksKeys = [];
            foreach ($keys as $keyString) {
                // Load the private key and get its details
                $privateKeyResource = openssl_pkey_get_private($keyString);
                $keyDetails = openssl_pkey_get_details($privateKeyResource);

                // Encode modulus and exponent for JWKS
                $n = rtrim(strtr(base64_encode($keyDetails['rsa']['n']), '+/', '-_'), '=');
                $e = rtrim(strtr(base64_encode($keyDetails['rsa']['e']), '+/', '-_'), '=');

                $jwksKeys[] = [
                    "kty" => "RSA",
                    "use" => "sig",
                    "kid" => "1", // Adjust this as needed for your key management
                    "n" => $n,
                    "e" => $e,
                ];
            }

            $jwks = ['keys' => $jwksKeys];
            echo json_encode($jwks);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;
    case '/register':
        if ($method == 'POST') {
            $requestData = json_decode(file_get_contents('php://input'), true);
            $username = $requestData['username'] ?? '';
            $email = $requestData['email'] ?? '';
            $password = bin2hex(random_bytes(16)); // Secure password generation
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
            if ($stmt->execute([':username' => $username, ':email' => $email, ':password_hash' => $passwordHash])) {
                http_response_code(201); // CREATED
                echo json_encode(['password' => $password]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to register user.']);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;

    case '/auth':
        if ($method == 'POST') {

        // IP-based rate limiting check
        $currentIP = $_SERVER['REMOTE_ADDR'];
        $currentTime = time();
        if (!isset($rateLimiterStorage[$currentIP])) {
            $rateLimiterStorage[$currentIP] = ['requests' => 0, 'time' => $currentTime];
        }
        if ($currentTime - $rateLimiterStorage[$currentIP]['time'] < $rateLimitWindow && $rateLimiterStorage[$currentIP]['requests'] >= $rateLimitMaxRequests) {
            http_response_code(429); // Too Many Requests
            echo json_encode(['error' => 'Too many requests. Please try again later.']);
            break;
        }
        if ($currentTime - $rateLimiterStorage[$currentIP]['time'] >= $rateLimitWindow) {
            $rateLimiterStorage[$currentIP] = ['requests' => 0, 'time' => $currentTime];
        }
        $rateLimiterStorage[$currentIP]['requests']++;

            // Determine if expired key is requested
            $expired = isset($_GET['expired']) && $_GET['expired'] === 'true';
            $keyCondition = $expired ? "<= :now" : ">= :now";

            // Prepare SQL to fetch the key
            if ($expired) {
                $sql = "SELECT key FROM keys WHERE exp <= :now ORDER BY exp DESC LIMIT 1";
            } else {
                $sql = "SELECT key FROM keys WHERE exp >= :now ORDER BY exp DESC LIMIT 1";
            }
            
            $stmt = $pdo->prepare($sql);
            $currentTime = time();
            $stmt->bindParam(':now', $currentTime, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            

            if ($row) {
                $privateKey = $row['key'];
                $kid = "1";
            } else {
                // Fallback to file-based key if no key is found in the database
                $privateKey = file_get_contents(__DIR__ . '/keys/private_key.pem');
                $kid = "1";
            }

            // Create JWT payload and encode token
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // Token valid for 1 hour
            $payload = [
                'iss' => 'http://localhost:8080',
                'aud' => 'http://localhost:8080',
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'sub' => '1234567890',
                'name' => 'John Doe',
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256', $kid);
            http_response_code(200);
            echo json_encode(['token' => $jwt]);

            $requestIP = $_SERVER['REMOTE_ADDR'];
            $logSQL = "INSERT INTO auth_logs (request_ip) VALUES (:request_ip)";
            $logStmt = $pdo->prepare($logSQL);
            if (!$logStmt->execute([':request_ip' => $requestIP])) {
               
                error_log("Failed to insert auth log for IP: $requestIP");
            }


        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
