<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\KeyInfoTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\KeyInfo
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfoTest extends TestCase
{
    /** @var string */
    private string $certificate;

    /** @var string[] */
    private array $certData;

    /** @var \DOMDocument */
    private DOMDocument $document;


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

        $this->certData = openssl_x509_parse(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::SELFSIGNED_PUBLIC_KEY)
        );

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_KeyInfo.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyInfo = new KeyInfo(
            [
                new KeyName('testkey'),
                new X509Data(
                    [
                        new X509Certificate($this->certificate),
                        new X509SubjectName($this->certData['name'])
                    ]
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ds:KeySomething>Some unknown tag within the ds-namespace</ds:KeySomething>'
                )->documentElement),
                new Chunk(DOMDocumentFactory::fromString('<some>Chunk</some>')->documentElement)
            ],
            'abc123'
        );

        $info = $keyInfo->getInfo();
        $this->assertCount(4, $info);
        $this->assertInstanceOf(KeyName::class, $info[0]);
        $this->assertInstanceOf(X509Data::class, $info[1]);
        $this->assertInstanceOf(Chunk::class, $info[2]);
        $this->assertInstanceOf(Chunk::class, $info[3]);
        $this->assertEquals('abc123', $keyInfo->getId());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($keyInfo));
    }


    /**
     */
    public function testMarshallingEmpty(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('ds:KeyInfo cannot be empty');

        $keyInfo = new KeyInfo([]);
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyInfo = KeyInfo::fromXML($this->document->documentElement);
        $this->assertEquals('abc123', $keyInfo->getId());

        $info = $keyInfo->getInfo();
        $this->assertCount(4, $info);
        $this->assertInstanceOf(KeyName::class, $info[0]);
        $this->assertInstanceOf(X509Data::class, $info[1]);
        $this->assertInstanceOf(Chunk::class, $info[2]);
        $this->assertInstanceOf(Chunk::class, $info[3]);
        $this->assertEquals('abc123', $keyInfo->getId());
    }


    /**
     */
    public function testUnmarshallingEmpty(): void
    {
        $document = DOMDocumentFactory::fromString('<ds:KeyInfo xmlns:ds="' . KeyInfo::NS . '"/>');

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('ds:KeyInfo cannot be empty');

        $keyInfo = KeyInfo::fromXML($document->documentElement);
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(KeyInfo::fromXML($this->document->documentElement))))
        );
    }
}