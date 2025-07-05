<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\{Base64BinaryValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{
    AbstractDsElement,
    AbstractKeyInfoType,
    KeyName,
    MgmtData,
    PGPData,
    PGPKeyID,
    PGPKeyPacket,
    SPKIData,
    SPKISexp,
    X509Certificate,
    X509Data,
    X509SubjectName,
};
use SimpleSAML\XMLSecurity\XML\xenc\{CarriedKeyName, P, RecipientKeyInfo, Seed};

use function dirname;
use function openssl_x509_parse;
use function str_replace;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\RecipientKeyInfoTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractKeyInfoType::class)]
#[CoversClass(RecipientKeyInfo::class)]
final class RecipientKeyInfoTest extends TestCase
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
        self::$testedClass = RecipientKeyInfo::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_RecipientKeyInfo.xml',
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
        $SPKISexp1 = new SPKISexp(
            Base64BinaryValue::fromString('GpM6'),
        );
        $seed = new Seed(
            CryptoBinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );
        $SPKISexp2 = new SPKISexp(
            Base64BinaryValue::fromString('GpM7'),
        );
        $SPKISexp3 = new SPKISexp(
            Base64BinaryValue::fromString('GpM8'),
        );
        $carriedKeyName = new CarriedKeyName(
            StringValue::fromString('Some label'),
        );

        $recipientKeyInfo = new RecipientKeyInfo(
            [
                new KeyName(
                    StringValue::fromString('testkey'),
                ),
                new X509Data(
                    [
                        new X509Certificate(
                            Base64BinaryValue::fromString(self::$certificate),
                        ),
                        new X509SubjectName(
                            StringValue::fromString(self::$certData['name']),
                        ),
                    ],
                ),
                new PGPData(
                    new PGPKeyID(
                        Base64BinaryValue::fromString('GpM7'),
                    ),
                    new PGPKeyPacket(
                        Base64BinaryValue::fromString('GpM8'),
                    ),
                    [
                        new P(
                            CryptoBinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                        ),
                    ],
                ),
                new SPKIData([
                    [$SPKISexp1, $seed],
                    [$SPKISexp2, null],
                    [$SPKISexp3, $carriedKeyName],
                ]),
                new MgmtData(
                    StringValue::fromString('ManagementData'),
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed654'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($recipientKeyInfo),
        );
    }


    /**
     */
    public function testMarshallingEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xenc:RecipientKeyInfo cannot be empty');

        new RecipientKeyInfo([]);
    }


    /**
     */
    public function testUnmarshallingEmpty(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<xenc:RecipientKeyInfo xmlns:xenc="' . RecipientKeyInfo::NS . '"/>',
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xenc:RecipientKeyInfo cannot be empty');

        RecipientKeyInfo::fromXML($document->documentElement);
    }
}
