<?php
use PHPUnit\Framework\TestCase;

class KeyGenerationTest extends TestCase
{
    public function testKeyFileExists()
    {
        $this->assertFileExists(__DIR__ . '/../keys_before_db/private_key.pem');
        $this->assertFileExists(__DIR__ . '/../keys_before_db/public_key.pem');
    }
}