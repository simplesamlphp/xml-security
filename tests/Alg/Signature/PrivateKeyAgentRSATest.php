<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Signature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\PrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Backend\SignatureBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\Signature\PrivateKeyAgentRSA.
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


    public function testSignWithCertificateCallsPkaBackend(): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->once())->method('sign')->willReturn('fakesig');

        $wrapper = new PrivateKeyAgentRSA(self::$certificate, C::SIG_RSA_SHA256, $pkaBackend);
        $result = $wrapper->sign('test data');

        $this->assertSame('fakesig', $result);
    }


    public function testVerifyWithCertificateDelegatesToPkaBackend(): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->once())->method('verify')->willReturn(true);
        $pkaBackend->expects($this->never())->method('sign');

        $wrapper = new PrivateKeyAgentRSA(self::$certificate, C::SIG_RSA_SHA256, $pkaBackend);
        $result = $wrapper->verify('data', 'sig');

        $this->assertTrue($result);
    }


    public function testSignWithPrivateKeyUsesLocalOpenSslNotPkaBackend(): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->never())->method('sign');
        $pkaBackend->expects($this->never())->method('setDigestAlg');

        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $wrapper = new PrivateKeyAgentRSA($privateKey, C::SIG_RSA_SHA256, $pkaBackend);

        $signature = $wrapper->sign('test data');
        $this->assertNotEmpty($signature);
    }


    public function testVerifyWithPublicKeyUsesLocalOpenSslNotPkaBackend(): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->never())->method('verify');
        $pkaBackend->expects($this->never())->method('setDigestAlg');

        // Produce a real signature with the private key for the verify assertion.
        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $plaintext = 'data for verify';
        $signature = $localBackend->sign($privateKey, $plaintext);

        $publicKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        $wrapper = new PrivateKeyAgentRSA($publicKey, C::SIG_RSA_SHA256, $pkaBackend);

        $this->assertTrue($wrapper->verify($plaintext, $signature));
    }


    public function testConstructWithForeignKeyTypeThrowsInvalidArgumentException(): void
    {
        $pkaBackend = $this->createStub(SignatureBackend::class);
        $this->expectException(InvalidArgumentException::class);
        new PrivateKeyAgentRSA(SymmetricKey::generate(16), C::SIG_RSA_SHA256, $pkaBackend);
    }


    /**
     * @return array<string, array{0: string}>
     */
    public static function providePssAndUnsupportedRsaAlgorithms(): array
    {
        return [
            'PSS SHA1' => [C::SIG_RSA_PSS_SHA1],
            'PSS SHA224' => [C::SIG_RSA_PSS_SHA224],
            'PSS SHA256' => [C::SIG_RSA_PSS_SHA256],
            'PSS SHA384' => [C::SIG_RSA_PSS_SHA384],
            'PSS SHA512' => [C::SIG_RSA_PSS_SHA512],
            'RIPEMD160' => [C::SIG_RSA_RIPEMD160],
        ];
    }


    #[DataProvider('providePssAndUnsupportedRsaAlgorithms')]
    public function testConstructWithCertificateAndNonV15AlgorithmThrowsUnsupportedAlgorithmException(
        string $algId,
    ): void {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->never())->method('sign');
        $pkaBackend->expects($this->never())->method('verify');
        $pkaBackend->expects($this->never())->method('setDigestAlg');

        $this->expectException(UnsupportedAlgorithmException::class);
        new PrivateKeyAgentRSA(self::$certificate, $algId, $pkaBackend);
    }


    public function testConstructWithPrivateKeyAndPssAlgorithmStillSignsLocally(): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->never())->method('sign');
        $pkaBackend->expects($this->never())->method('setDigestAlg');

        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $wrapper = new PrivateKeyAgentRSA($privateKey, C::SIG_RSA_PSS_SHA256, $pkaBackend);

        $signature = $wrapper->sign('test data');
        $this->assertNotEmpty($signature);
    }


    /**
     * @return array<string, array{0: string}>
     */
    public static function provideV15Algorithms(): array
    {
        return [
            'SHA1' => [C::SIG_RSA_SHA1],
            'SHA224' => [C::SIG_RSA_SHA224],
            'SHA256' => [C::SIG_RSA_SHA256],
            'SHA384' => [C::SIG_RSA_SHA384],
            'SHA512' => [C::SIG_RSA_SHA512],
        ];
    }


    #[DataProvider('provideV15Algorithms')]
    public function testConstructWithCertificateAndV15AlgorithmStillCallsPkaBackend(string $algId): void
    {
        $pkaBackend = $this->createMock(SignatureBackend::class);
        $pkaBackend->expects($this->once())->method('sign')->willReturn('fakesig');

        $wrapper = new PrivateKeyAgentRSA(self::$certificate, $algId, $pkaBackend);
        $this->assertSame('fakesig', $wrapper->sign('test data'));
    }


    public function testGetSupportedAlgorithmsMirrorsRsaDigests(): void
    {
        $this->assertSame(array_keys(C::$RSA_DIGESTS), PrivateKeyAgentRSA::getSupportedAlgorithms());
    }
}
