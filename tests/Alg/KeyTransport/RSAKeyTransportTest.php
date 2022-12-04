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
    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected PrivateKey $privateKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected PublicKey $publicKey;

    /** @var string */
    protected string $plaintext = 'plaintext';

    /** @var \SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory */
    protected KeyTransportAlgorithmFactory $factory;


    /**
     *
     */
    public function setUp(): void
    {
        $this->publicKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        $this->privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $this->factory = new KeyTransportAlgorithmFactory([]);
    }


    /**
     * Test encrypting with RSA.
     */
    public function testEncrypt(): void
    {
        // test RSA 1.5
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, $this->publicKey);
        $encrypted = $rsa->encrypt($this->plaintext);
        $this->assertNotEmpty($encrypted);
        $this->assertEquals(128, strlen($encrypted));

        // test RSA-OAEP
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, $this->publicKey);
        $encrypted = $rsa->encrypt($this->plaintext);
        $this->assertNotEmpty($encrypted);
        $this->assertEquals(128, strlen($encrypted));

        // test RSA-OAEP-MGF1P
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->publicKey);
        $encrypted = $rsa->encrypt($this->plaintext);
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
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);

        // test RSA-OAEP (should behave the same as MGF1P)
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);

        // test RSA-1.5
        $ciphertext = "ZAnYBqqM5T/kg+P8fb3UfDU1gyUIpndpqQN2qpmJso2z6His6WOkh5JFVN/wz+agvyR54kMmII" .
                      "afiDsy5izSk6+QZ5kMOgRLrmnh+RYZXjvCL6i1NXzaLw8yZLBvlP01SNMv/BBq640yzbG9U2ZN" .
                      "nxBLDvBmbJBxzt6XCowXQS8=";
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);
    }
}
