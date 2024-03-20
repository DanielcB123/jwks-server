<?php

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

// Prepare database connection
$dbFile = 'totally_not_my_privateKeys.db';
$pdo = new PDO('sqlite:' . $dbFile);

// Prepare SQL statement
$sql = "INSERT INTO keys (key, exp) VALUES (:key, :exp)";

// Insert keys into the database
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
