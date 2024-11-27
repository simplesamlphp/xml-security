<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\AbstractKeyInfoType;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\XML\xenc\OriginatorKeyInfo;

use function dirname;
use function openssl_x509_parse;
use function str_replace;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\OriginatorKeyInfoTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractKeyInfoType::class)]
#[CoversClass(OriginatorKeyInfo::class)]
final class OriginatorKeyInfoTest extends TestCase
{
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;


    /**
     */
    public function setUp(): void
    {
        self::$testedClass = OriginatorKeyInfo::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_OriginatorKeyInfo.xml',
        );

        self::$certificate = str_replace(
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
                '',
            ],
            PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::SELFSIGNED_CERTIFICATE),
        );

        self::$certData = openssl_x509_parse(
            PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::SELFSIGNED_CERTIFICATE),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $originatorKeyInfo = new OriginatorKeyInfo(
            [
                new KeyName('testkey'),
                new X509Data(
                    [
                        new X509Certificate(self::$certificate),
                        new X509SubjectName(self::$certData['name']),
                    ],
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                )->documentElement),
            ],
            'fed654',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($originatorKeyInfo),
        );
    }


    /**
     */
    public function testMarshallingEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xenc:OriginatorKeyInfo cannot be empty');

        new OriginatorKeyInfo([]);
    }


    /**
     */
    public function testUnmarshallingEmpty(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<xenc:OriginatorKeyInfo xmlns:xenc="' . OriginatorKeyInfo::NS . '"/>',
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xenc:OriginatorKeyInfo cannot be empty');

        OriginatorKeyInfo::fromXML($document->documentElement);
    }
}