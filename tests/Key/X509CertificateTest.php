<?php

namespace SimpleSAML\XMLSecurity\Key;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

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
        $this->f = file_get_contents('tests/mycert.pem');
        $this->cert = openssl_pkey_get_details(openssl_pkey_get_public(openssl_x509_read($this->f)));
        $this->c = new X509Certificate($this->f);
    }


    /**
     * Cover basic creation and retrieval.
     */
    public function testCreation(): void
    {
        $pubDetails = openssl_pkey_get_details($this->c->get());
        $this->assertEquals($this->cert['key'], $pubDetails['key']);
    }


    /**
     * Test for retrieval of the PEM-encoded certificate.
     */
    public function testGetCertificate(): void
    {
        $this->assertEquals($this->f, $this->c->getCertificate());
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
        if (!function_exists('openssl_x509_fingerprint')) {
            $this->markTestSkipped();
        }

        $f = openssl_x509_fingerprint($this->f);
        $this->assertEquals($f, $this->c->getRawThumbprint());

        $m = new ReflectionMethod(X509Certificate::class, 'manuallyComputeThumbprint');
        $m->setAccessible(true);
        $this->assertEquals($f, $m->invokeArgs($this->c, [C::DIGEST_SHA1]));
    }


    /**
     * Test thumbprint generation with an invalid digest algorithm.
     */
    public function testGetRawThumbprintWithWrongAlg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->c->getRawThumbprint('invalid');
    }


    /**
     * Test creation from a file containing the PEM-encoded certificate.
     */
    public function testFromFile(): void
    {
        $c = X509Certificate::fromFile('tests/mycert.pem');
        $pubDetails = openssl_pkey_get_details($c->get());
        $this->assertEquals($this->cert['key'], $pubDetails['key']);
    }
}
