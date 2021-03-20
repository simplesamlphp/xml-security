<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use SimpleSAML\Test\XML\SerializableXMLTest;
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
final class X509CertificateTest extends SerializableXMLTest
{
    /** @var string */
    private string $certificate;


    /**
     */
    public function setUp(): void
    {
        self::$element = X509Certificate::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509Certificate.xml'
        );

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
    }


    /**
     */
    public function testMarshalling(): void
    {
        $X509cert = new X509Certificate($this->certificate);

        $this->assertEquals($this->certificate, $X509cert->getCertificate());

        $this->assertEquals(self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement), strval($X509cert));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $X509cert = X509Certificate::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals($this->certificate, $X509cert->getCertificate());
    }
}
