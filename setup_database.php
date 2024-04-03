<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Create (connect to) SQLite database in file and create tables if they don't exist
$dbFile = 'totally_not_my_privateKeys.db';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create keys table
    $tableSchemaKeys = "CREATE TABLE IF NOT EXISTS keys(
        kid INTEGER PRIMARY KEY AUTOINCREMENT,
        key BLOB NOT NULL,
        exp INTEGER NOT NULL
    )";
    $pdo->exec($tableSchemaKeys);
    
    // Create users table
    $userTableSchema = "CREATE TABLE IF NOT EXISTS users(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        email TEXT UNIQUE,
        date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP      
    )";
    $pdo->exec($userTableSchema);

    // Create auth_logs table
    $authLogsTableSchema = "CREATE TABLE IF NOT EXISTS auth_logs(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        request_ip TEXT NOT NULL,
        request_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INTEGER,  
        FOREIGN KEY(user_id) REFERENCES users(id)
    )";
    $pdo->exec($authLogsTableSchema);

    echo "Database and tables created successfully.\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}

// Configuration for generating the private key
$config = [
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];

// Generate two private keys
$keys = [];
for ($i = 0; $i < 2; $i++) {
    $privateKeyResource = openssl_pkey_new($config);
    openssl_pkey_export($privateKeyResource, $privateKeyString);
    // Consider encrypting the privateKeyString here with AES before storing
    $keys[] = $privateKeyString; // This should be your encrypted key
}

// Prepare SQL statement for inserting keys
$sql = "INSERT INTO keys (key, exp) VALUES (:key, :exp)";
$stmt = $pdo->prepare($sql);

// Insert the first key (expires now)
$stmt->execute([
    ':key' => $keys[0], // Assume this is your encrypted key
    ':exp' => time() // Expires now
]);

// Insert the second key (expires in 1 hour)
$stmt->execute([
    ':key' => $keys[1], // And this
    ':exp' => time() + 3600 // Expires in 1 hour
]);

echo "Keys stored in database successfully.\n";
?>
