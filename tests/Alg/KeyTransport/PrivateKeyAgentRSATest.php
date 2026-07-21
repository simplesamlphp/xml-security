<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\KeyTransport;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\PrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\KeyTransport\PrivateKeyAgentRSA.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentRSATest extends TestCase
{
    private static X509Certificate $certificate;


    public static function setUpBeforeClass(): void
    {
        self::$certificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
    }


    public function testDecryptWithCertificateCallsPkaBackend(): void
    {
        $pkaBackend = $this->createMock(EncryptionBackend::class);
        $pkaBackend->expects($this->once())->method('decrypt')->willReturn('decrypted');

        $wrapper = new PrivateKeyAgentRSA(self::$certificate, C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);
        $result = $wrapper->decrypt('ciphertext');

        $this->assertSame('decrypted', $result);
    }


    public function testEncryptWithCertificateDelegatesToPkaBackend(): void
    {
        $pkaBackend = $this->createMock(EncryptionBackend::class);
        $pkaBackend->expects($this->once())->method('encrypt')->willReturn('encrypted');
        $pkaBackend->expects($this->never())->method('decrypt');

        $wrapper = new PrivateKeyAgentRSA(self::$certificate, C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);
        $result = $wrapper->encrypt('plaintext');

        $this->assertSame('encrypted', $result);
    }


    public function testConstructWithPrivateKeyDoesNotUsePkaBackend(): void
    {
        $pkaBackend = $this->createMock(EncryptionBackend::class);
        $pkaBackend->expects($this->never())->method('decrypt');
        $pkaBackend->expects($this->never())->method('encrypt');
        $pkaBackend->expects($this->never())->method('setCipher');

        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        new PrivateKeyAgentRSA($privateKey, C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);

        $this->addToAssertionCount(1); // Constructor succeeded without calling pkaBackend.
    }


    public function testConstructWithForeignKeyTypeThrowsInvalidArgumentException(): void
    {
        $pkaBackend = $this->createStub(EncryptionBackend::class);
        $this->expectException(InvalidArgumentException::class);
        new PrivateKeyAgentRSA(SymmetricKey::generate(16), C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);
    }


    public function testGetSupportedAlgorithmsMirrorsKeyTransportAlgorithms(): void
    {
        $this->assertSame(C::$KEY_TRANSPORT_ALGORITHMS, PrivateKeyAgentRSA::getSupportedAlgorithms());
    }
}
