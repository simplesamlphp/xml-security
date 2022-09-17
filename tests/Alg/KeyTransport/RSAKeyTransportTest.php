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
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->privateKey);
    }


    /**
     * Test decrypting with RSA.
     */
    public function testDecrypt(): void
    {
        // test RSA-OAEP-MGF1P
        $ciphertext = "j/siG2qn4/YQWVXRN6QuwTz1nm1l7fhCO1tqC0j6wkBhubeMbpvoF2rhRPXloy/1IYtHLubuWyrdYrXrxX/eyrPHonsSb0Y"
                    . "/RUMYS4/s157o2vaJB2RYE1D9A5GACsJiDwD2NGrTecymySUjB84gwp32yMfoISlc9+vneTgfNnk=";
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);

        // test RSA-OAEP (should behave the same as MGF1P)
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);

        // test RSA-1.5
        $ciphertext = "XACuRtMaqFyalcp6/wxA6dKM6kpNHTs/yLlj28vV5JshXLFbBAm8YlxeuuniE6m2+DBN78WdIb+2mJYDNSeKitO7NYug7"
                    . "hKK0occdolFHEZMgX8Nf+cqPCOeclvwyWwMzA+oGrIywhBEpD5kojjhR4UcbGKB5ghmPVKEzJi5cf0=";
        $rsa = $this->factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, $this->privateKey);
        $plaintext = $rsa->decrypt(base64_decode($ciphertext));
        $this->assertEquals($this->plaintext, $plaintext);
    }
}
