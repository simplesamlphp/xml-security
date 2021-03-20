<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509CertificateTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509Certificate
 *
 * @package simplesamlphp/xml-security
 */
final class X509CertificateTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;

    /** @var string */
    private string $certificate;


    /**
     */
    public function setUp(): void
    {
        $this->certificate = str_replace(
            [
                '-----BEGIN CERTIFICATE-----',
                '-----END CERTIFICATE-----',
                '-----BEGIN RSA PUBLIC KEY-----',
                '-----END RSA PUBLIC KEY-----',
                "\r\n",
                "\n",
            ],
            [
                '',
                '',
                '',
                '',
                "\n",
                ''
            ],
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::SELFSIGNED_PUBLIC_KEY)
        );

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509Certificate.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $X509cert = new X509Certificate($this->certificate);

        $this->assertEquals($this->certificate, $X509cert->getCertificate());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($X509cert));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $X509cert = X509Certificate::fromXML($this->document->documentElement);

        $this->assertEquals($this->certificate, $X509cert->getCertificate());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(X509Certificate::fromXML($this->document->documentElement))))
        );
    }
}