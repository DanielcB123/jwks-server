<?php

// Ensure the keys directory exists
if (!file_exists(__DIR__ . '/keys')) {
    mkdir(__DIR__ . '/keys', 0700, true);
}

$privateKeyPath = __DIR__ . '/keys/private_key.pem';
$publicKeyPath = __DIR__ . '/keys/public_key.pem';
$metadataPath = __DIR__ . '/keys/metadata.json';

// Generate private key
$privateKeyResource = openssl_pkey_new([
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
]);
openssl_pkey_export_to_file($privateKeyResource, $privateKeyPath);

// Extract the public key
$keyDetails = openssl_pkey_get_details($privateKeyResource);
file_put_contents($publicKeyPath, $keyDetails['key']);

// Store key metadata with an expiration date
$expirationDate = date('c', strtotime('+1 year')); // Set expiration 1 year from now
$metadata = [
    'kid' => '1', // Example key ID, match with your JWT encoding
    'expires' => $expirationDate,
];
file_put_contents($metadataPath, json_encode($metadata));

echo "RSA keys and metadata generated successfully.\n";
