Overview
This project implements a JWT authentication system using an SQLite-backed JWKS server. The server generates and uses RSA keys for JWT encoding and decoding, and stores these keys in an SQLite database for persistence. The system includes endpoints for token generation (/auth) and providing public keys in JWKS format (/.well-known/jwks.json).

Components
1. SQLite Database Setup
File Name: totally_not_my_privateKeys.db
Description: A SQLite database file that stores RSA private keys and their expiration times.
Schema:
kid: An auto-incremented integer primary key.
key: A BLOB to store the RSA private key.
exp: An integer to store the expiration time of the key.
2. Key Generation Script
Purpose: Generates RSA private keys and stores them in the SQLite database.
Key Characteristics:
Key Size: 2048 bits
Key Type: RSA
Functionality:
Generates two keys: one that expires immediately and another that expires in one hour.
Stores these keys in the database with their respective expiration times.
3. Server Implementation
Core File: server.php (or a similar named PHP file)
Dependencies: vendor/autoload.php for loading external libraries (e.g., Firebase JWT).
Database Connection: Established using PDO to interact with the SQLite database.
HTTP Endpoints:
/.well-known/jwks.json: Provides public key details in JWKS format.
/auth: Issues JWTs signed with keys from the database. Handles an expired query parameter to issue tokens with either valid or expired keys.
4. Endpoints Description
/.well-known/jwks.json
Method: GET
Response Type: JSON
Functionality: Fetches all valid (non-expired) keys from the database and returns their public parts in JWKS format.
/auth
Method: POST
Response Type: JSON
Functionality:
Reads a private key from the database.
If the expired query parameter is not present, uses a valid (unexpired) key.
If expired=true, uses an expired key.
Signs a JWT with the selected private key and returns the JWT.
5. Security Considerations
SQL Injection Protection: All database queries are parameterized to prevent SQL injection.
Error Handling: Proper error handling is implemented to avoid revealing sensitive information.
Key Management: Keys are managed carefully with expiration logic to enhance security.
Testing
Unit Testing: Includes tests for key generation, database operations, JWT issuance, and JWKS endpoint responses.
Integration Testing: Ensures the system works as a whole, especially focusing on the interaction between the database and the server endpoints.
Security Testing: Tests for vulnerabilities, particularly SQL injection and improper key usage.
Deployment