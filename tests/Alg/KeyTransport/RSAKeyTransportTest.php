<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\KeyTransport;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\KeyTransport\RSA.
 *
 * @package simplesamlphp/xml-security
 */
class RSAKeyTransportTest extends TestCase
{
    /** @var string */
    public const PLAINTEXT = 'plaintext';

    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected static PrivateKey $privateKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $publicKey;

    /** @var \SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory */
    protected static KeyTransportAlgorithmFactory $factory;


    /**
     *
     */
    public static function setUpBeforeClass(): void
    {
        self::$publicKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        self::$privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        self::$factory = new KeyTransportAlgorithmFactory([]);
    }


    /**
     * Test encrypting with RSA.
     */
    public function testEncrypt(): void
    {
        // test RSA 1.5
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$publicKey);
        $encrypted = $rsa->encrypt(self::PLAINTEXT);
        $this->assertNotEmpty($encrypted);
        $this->assertEquals(128, strlen($encrypted));

        // test RSA-OAEP
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$publicKey);
        $encrypted = $rsa->encrypt(self::PLAINTEXT);
        $this->assertNotEmpty($encrypted);
        $this->assertEquals(128, strlen($encrypted));

        // test RSA-OAEP-MGF1P
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$publicKey);
        $encrypted = $rsa->encrypt(self::PLAINTEXT);
        $this->assertNotEmpty($encrypted);
        $this->assertEquals(128, strlen($encrypted));
    }


    /**
     * Test decrypting with RSA.
     *
     * NOTE: if you change the key material, you have to replace $ciphertext with a
     *       base64 encoded version of the $encrypted var from ::testEncrypt
     */
    public function testDecrypt(): void
    {
        // test RSA-OAEP-MGF1P
        $ciphertext = "0Ok/N3BV5LUxmr8IDXQQhtzQEJzD5uSN5kOVjzPkzesjlSVR9qv819MPBL8yfSMdUSQWVq1N/w" .
                      "A6fgclGb/keGZOtjSkHZnZEZvXEOQItFjS6MbQc+TzNmRd6FSkuPUmwQ1V+NwxTPCIwXSSd0Aj" .
                      "7oHb7xRdBhoFuDrSbYAvATQ=";
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals(self::PLAINTEXT, $plaintext);

        // test RSA-OAEP (should behave the same as MGF1P)
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals(self::PLAINTEXT, $plaintext);

        // test RSA-1.5
        $ciphertext = "ZAnYBqqM5T/kg+P8fb3UfDU1gyUIpndpqQN2qpmJso2z6His6WOkh5JFVN/wz+agvyR54kMmII" .
                      "afiDsy5izSk6+QZ5kMOgRLrmnh+RYZXjvCL6i1NXzaLw8yZLBvlP01SNMv/BBq640yzbG9U2ZN" .
                      "nxBLDvBmbJBxzt6XCowXQS8=";
        $rsa = self::$factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals(self::PLAINTEXT, $plaintext);
    }
}
