<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Encryption;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\Encryption\AES.
 *
 * @package simplesamlphp/xml-security
 */
class AESEncryptionTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $skey;

    /** @var \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory */
    protected static EncryptionAlgorithmFactory $factory;

    /** @var \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface */
    protected static EncryptionAlgorithmInterface $algo;


    public static function setUpBeforeClass(): void
    {
        self::$skey = new SymmetricKey(hex2bin('7392d8ec40a862fbb02786bab41481b2'));
        self::$factory = new EncryptionAlgorithmFactory([]);
        self::$algo = self::$factory->getAlgorithm(C::BLOCK_ENC_AES128, self::$skey);
    }


    /**
     * Test AES encryption.
     */
    public function testEncrypt(): void
    {
        $ciphertext = self::$algo->encrypt('plaintext');
        $this->assertNotEmpty($ciphertext);
        $this->assertEquals(32, strlen($ciphertext));
    }


    /**
     * Test AES decryption.
     */
    public function testDecrypt(): void
    {
        $ciphertext = "r0YRkEixBnAKU032/ux7avHcVTH1CIIyKaPA2qr4KlIs0LVZp5CuwQKRRi6lji4cnaFbH4jETtJhMSEfbpSdvg==";
        $plaintext = self::$algo->decrypt(base64_decode($ciphertext, true));
        $this->assertEquals("\n  <Value>\n\tHello, World!\n  </Value>\n", $plaintext);
    }
}
