<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509Digest;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\X509DataTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509Data
 *
 * @package simplesamlphp/xml-security
 */
final class X509DataTest extends TestCase
{
    use SerializableXMLTestTrait;

    /** @var string */
    private string $certificate;

    /** @var string[] */
    private array $certData;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    private Key\X509Certificate $key;

    /** @var string */
    private string $digest;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = X509Data::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509Data.xml'
        );

        $this->key = new Key\X509Certificate(
            PEMCertificatesMock::getPlainPublicKey()
        );

        $this->digest = base64_encode(hex2bin($this->key->getRawThumbprint(Constants::DIGEST_SHA256)));

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

        $this->certData = openssl_x509_parse(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::SELFSIGNED_PUBLIC_KEY)
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $x509data = new X509Data(
            [
                new Chunk(
                    DOMDocumentFactory::fromString('<ds:X509UnknownTag>somevalue</ds:X509UnknownTag>')->documentElement
                ),
                new X509Certificate($this->certificate),
                new X509Digest($this->digest, Constants::DIGEST_SHA256),
                new X509SubjectName($this->certData['name']),
                new Chunk(DOMDocumentFactory::fromString('<some>Chunk</some>')->documentElement)
            ]
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($X509data)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $x509data = X509Data::fromXML($this->xmlRepresentation->documentElement);

        $data = $x509data->getData();
        $this->assertInstanceOf(Chunk::class, $data[0]);
        $this->assertInstanceOf(X509Certificate::class, $data[1]);
        $this->assertInstanceOf(X509Digest::class, $data[2]);
        $this->assertInstanceOf(X509SubjectName::class, $data[3]);
        $this->assertInstanceOf(Chunk::class, $data[4]);
    }
}
