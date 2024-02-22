<?php
use PHPUnit\Framework\TestCase;

class KeyGenerationTest extends TestCase
{
    public function testKeyFileExists()
    {
        $this->assertFileExists(__DIR__ . '/../keys/private_key.pem');
        $this->assertFileExists(__DIR__ . '/../keys/public_key.pem');
    }
}
