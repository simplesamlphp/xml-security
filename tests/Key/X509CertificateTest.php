<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Key;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function file_get_contents;
use function openssl_pkey_get_details;
use function openssl_pkey_get_public;
use function openssl_x509_fingerprint;
use function openssl_x509_parse;
use function openssl_x509_read;

/**
 * Test for SimpleSAML\XMLSecurity\Key\X509Certificate
 *
 * @package SimpleSAML\XMLSecurity\Key
 */
final class X509CertificateTest extends TestCase
{
    /** @var resource */
    protected $cert;

    /** @var string */
    protected string $f;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    protected X509Certificate $c;


    /**
     * Initialize the test by loading the file ourselves.
     */
    protected function setUp(): void
    {
        $this->f = PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::CERTIFICATE);
        $this->cert = openssl_pkey_get_details(openssl_pkey_get_public(openssl_x509_read($this->f)));
        $this->c = new X509Certificate(PEM::fromString($this->f));
    }


    /**
     * Cover basic creation and retrieval.
     */
    public function testCreation(): void
    {
        $pubDetails = openssl_pkey_get_details(openssl_pkey_get_public($this->c->getMaterial()));
        $this->assertEquals($this->cert['key'], $pubDetails['key']);
    }


    /**
     * Test for retrieval of the PEM-encoded certificate.
     */
    public function testGetCertificate(): void
    {
        $this->assertEquals($this->f, $this->c->getMaterial());
    }


    /**
     * Test for retrieval of the certificate's details.
     */
    public function testGetCertificateDetails(): void
    {
        $this->assertEquals(openssl_x509_parse($this->f), $this->c->getCertificateDetails());
    }


    /**
     * Test thumbprint generation from a certificate.
     */
    public function testGetRawThumbprint(): void
    {
        $f = openssl_x509_fingerprint($this->f);
        $this->assertEquals($f, $this->c->getRawThumbprint());
    }


    /**
     * Test thumbprint generation with an invalid digest algorithm.
     */
    public function testGetRawThumbprintWithWrongAlg(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $this->c->getRawThumbprint('invalid');
    }


    /**
     * Test creation from a file containing the PEM-encoded certificate.
     */
    public function testFromFile(): void
    {
        $c = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pubDetails = openssl_pkey_get_details(openssl_pkey_get_public($c->getMaterial()));
        $this->assertEquals($this->cert['key'], $pubDetails['key']);
    }
}
