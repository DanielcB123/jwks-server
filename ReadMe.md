# JWKS Server Project

This JWKS (JSON Web Key Set) server is a simple implementation designed to serve public keys in JWKS format and handle authentication requests, issuing JWTs (JSON Web Tokens) upon POST requests to the `/auth` endpoint.

## Dependencies

- **PHP**: The project is built with PHP. Ensure you have PHP 8.2.4 or newer installed.
- **OpenSSL**: Used for generating RSA key pairs.
- **Composer**: PHP's dependency manager, used for installing the JWT handling library.

## Setup

### 1. Install PHP

Ensure PHP is installed on your system. You can download it from [the official PHP website](https://www.php.net/downloads.php). Add PHP to your system's PATH environment variable to run PHP commands from any command prompt.

### 2. Install OpenSSL

OpenSSL is required to generate RSA keys. If not already installed, download and install OpenSSL from [the official source](https://www.openssl.org/source/) or use a binary distribution suitable for Windows, such as the one provided by [Shining Light Productions](https://slproweb.com/products/Win32OpenSSL.html).

### 3. Install Composer

Download and install Composer from [getcomposer.org](https://getcomposer.org/download/). Follow the installation instructions to make Composer available globally on your system.

## Project Structure

jwks-server/
├── keys/
│ ├── private_key.pem
│ └── public_key.pem
├── vendor/
├── composer.json
├── composer.lock
└── server.php


## Generating RSA Keys

In your project directory, use OpenSSL to generate the RSA keys:

    openssl genrsa -out keys/private_key.pem 2048
    openssl rsa -in keys/private_key.pem -pubout -out keys/public_key.pem


## Installing PHP Dependencies

Navigate to your project directory and run:

    composer require firebase/php-jwt

This installs the necessary library for JWT handling.


## Running the Server

From the root of your project directory, start the PHP built-in server:

    php -S localhost:8080 server.php


## Testing the Server

### JWKS Endpoint

Use PowerShell or another HTTP client to GET the JWKS:

    Invoke-WebRequest -Uri "http://localhost:8080/.well-known/jwks.json" -Method Get



### Authentication Endpoint

Issue a POST request to receive a JWT:

    Invoke-WebRequest -Uri "http://localhost:8080/auth" -Method Post