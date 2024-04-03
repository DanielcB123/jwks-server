# JWKS Server Project

This JWKS (JSON Web Key Set) server is an advanced implementation designed to serve public keys in JWKS format, handle authentication requests, issue JWTs (JSON Web Tokens) upon successful authentication, register new users, and optionally rate-limit authentication requests to enhance security.

## Dependencies

- **PHP**: The project is built with PHP. Ensure you have PHP 8.2.4 or newer installed.
- **SQLite3**: Used for storing encrypted private keys, user credentials, and authentication logs.
- **OpenSSL**: Utilized for generating RSA key pairs and encrypting private keys.
- **Composer**: PHP's dependency manager, used for installing the JWT handling library.

## Setup

### 1. Install PHP

Ensure PHP and SQLite3 support are installed on your system. You can download PHP from [the official PHP website](https://www.php.net/downloads.php). Add PHP to your system's PATH environment variable to run PHP commands from any command prompt.

### 2. Install OpenSSL

OpenSSL is required for RSA key generation and private key encryption. If not already installed, you can download and install OpenSSL from [the official source](https://www.openssl.org/source/).

### 3. Install Composer

Download and install Composer from [getcomposer.org](https://getcomposer.org/download/). Follow the installation instructions to make Composer available globally on your system.

## Project Structure

jwks-server/
├── vendor/
├── composer.json
├── composer.lock
├── setup_database.php
└── server.php


## Initializing the Database

In the project directory, initialize the database and tables, and generate encrypted RSA keys using:

    php setup_database.php

## Installing PHP Dependencies

Navigate to the project directory and run:

    composer require firebase/php-jwt

This command installs the necessary library for JWT handling.

## Running the Server

From the root of the project directory, start the PHP built-in server using:

    php -S 127.0.0.1:8080 server.php

## Testing the Server

### JWKS Endpoint

To retrieve the public keys in JWKS format:

    Invoke-WebRequest -Uri "http://127.0.0.1:8080/.well-known/jwks.json" -Method Get

### Authentication Endpoint

To authenticate and receive a JWT:

    Invoke-WebRequest -Uri "http://127.0.0.1:8080/auth" -Method Post

### User Registration Endpoint

To register a new user and receive a generated password:

    Invoke-WebRequest -Uri "http://127.0.0.1:8080/register" -Method Post -Body '{"username": "newUser", "email": "newUser@example.com"}' -ContentType "application/json"

## Rate Limiting (Optional)

The `/auth` endpoint includes an optional rate-limiting feature to enhance security by preventing abuse. It's configured to limit authentication requests to 10 per second per IP address.

## Testing with PHPUnit

This project uses PHPUnit for unit testing to ensure code reliability and robustness. After installing PHPUnit via Composer, you can run tests with:

    vendor/bin/phpunit

### Test Coverage

Aiming for over 80% test coverage, this project prioritizes maintaining high-quality code. Generate a coverage report in HTML format using:

    vendor/bin/phpunit --coverage-html coverage-report

### GradeBot Test

Run the following command in the project's root directory to perform automated testing:

    ./gradebot project2
