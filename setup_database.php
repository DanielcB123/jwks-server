<?php

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Create (connect to) SQLite database in file and create table if it doesn't exist
$dbFile = 'totally_not_my_privateKeys.db';
$tableSchema = "CREATE TABLE IF NOT EXISTS keys(
    kid INTEGER PRIMARY KEY AUTOINCREMENT,
    key BLOB NOT NULL,
    exp INTEGER NOT NULL
)";

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($tableSchema);
    echo "Database and table created successfully.\n";
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
    $keys[] = $privateKeyString;
}

// Prepare SQL statement for inserting keys
$sql = "INSERT INTO keys (key, exp) VALUES (:key, :exp)";
$stmt = $pdo->prepare($sql);

// Insert the first key (expires now)
$stmt->execute([
    ':key' => $keys[0],
    ':exp' => time() // Expires now
]);

// Insert the second key (expires in 1 hour)
$stmt->execute([
    ':key' => $keys[1],
    ':exp' => time() + 3600 // Expires in 1 hour
]);

echo "Keys stored in database successfully.\n";
?>
