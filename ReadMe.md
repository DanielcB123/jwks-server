# JWKS Server Project

This JWKS (JSON Web Key Set) server is a simple implementation designed to serve public keys in JWKS format and handle authentication requests, issuing JWTs (JSON Web Tokens) upon POST requests to the `/auth` endpoint.

## Dependencies

- **PHP**: The project is built with PHP. Ensure you have PHP 8.2.4 or newer installed.
- **OpenSSL**: Used for generating RSA key pairs.
- **Composer**: PHP's dependency manager, used for installing the JWT handling library.

## Setup

### 1. Install PHP

Ensure PHP is installed on our system. You can download it from [the official PHP website](https://www.php.net/downloads.php). Add PHP to our system's PATH environment variable to run PHP commands from any command prompt.

### 2. Install OpenSSL

OpenSSL is required to generate RSA keys. If not already installed, download and install OpenSSL from [the official source](https://www.openssl.org/source/) or use a binary distribution suitable for Windows, such as the one provided by [Shining Light Productions](https://slproweb.com/products/Win32OpenSSL.html).

### 3. Install Composer

Download and install Composer from [getcomposer.org](https://getcomposer.org/download/). Follow the installation instructions to make Composer available globally on our system.

## Project Structure

```
jwks-server/
├── keys/
│ ├── private_key.pem
│ └── public_key.pem
├── vendor/
├── composer.json
├── composer.lock
└── server.php
```

## Generating RSA Keys

In our project directory, use our generate_keys.php to generate our .pem keys by using the command:

    php generate_keys.php

## Installing PHP Dependencies

Navigate to our project directory and run:

    composer require firebase/php-jwt

This installs the necessary library for JWT handling.


## Running the Server

From the root of our project directory, start the PHP built-in server:

    php -S localhost:8080 server.php


## Testing the Server

### JWKS Endpoint

Use PowerShell or another HTTP client to GET the JWKS:

    Invoke-WebRequest -Uri "http://localhost:8080/.well-known/jwks.json" -Method Get



### Authentication Endpoint

Issue a POST request to receive a JWT:

    Invoke-WebRequest -Uri "http://localhost:8080/auth" -Method Post


## Testing with PHPUnit

This project uses PHPUnit for testing to ensure reliability and robustness of the codebase. PHPUnit is a programmer-oriented testing framework for PHP. It is an instance of the xUnit architecture for unit testing frameworks.

### Running Tests

To run the tests, ensure PHPUnit is installed via Composer as a dev dependency. You can do so by running:

    composer require --dev phpunit/phpunit

After installing PHPUnit, you can run the tests using the following command:

    vendor/bin/phpunit


### Test Coverage

Ensuring high test coverage is crucial for maintaining code quality and detecting issues early. This project aims for over 80% test coverage. To generate a test coverage report, you'll need to have Xdebug or pcov installed and configured with our PHP installation.

Run the following command to generate a coverage report in HTML format:

    vendor/bin/phpunit --coverage-html coverage-report


