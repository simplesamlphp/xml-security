<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Encryption;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\Encryption\TripleDES.
 *
 * @package simplesamlphp/xml-security
 */
class TripleDesEncryptionTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $skey;

    /** @var \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory */
    protected static EncryptionAlgorithmFactory $factory;

    /** @var \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface */
    protected static EncryptionAlgorithmInterface $algo;


    public static function setUpBeforeClass(): void
    {
        self::$skey = new SymmetricKey(hex2bin('0d6d02528a57fd9797a79db307ce1558761a454a1c1f4a57'));
        self::$factory = new EncryptionAlgorithmFactory([]);
        self::$algo = self::$factory->getAlgorithm(C::BLOCK_ENC_3DES, self::$skey);
    }


    /**
     * Test 3DES encryption.
     */
    public function testEncrypt(): void
    {
        $ciphertext = self::$algo->encrypt('plaintext');
        $this->assertNotEmpty($ciphertext);
        $this->assertEquals(24, strlen($ciphertext));
    }


    /**
     * Test 3DES decryption.
     */
    public function testDecrypt(): void
    {
        $ciphertext = "D+3dKq7MFK7U+8bqdlyRcvO12JV5Lahl5ALhF5eJXSfi+cbYKRbkRjvJsMKPp2Mk";
        $plaintext = self::$algo->decrypt(base64_decode($ciphertext));
        $this->assertEquals("\n  <Value>\n\tHello, World!\n  </Value>\n", $plaintext);
    }
}
