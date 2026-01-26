<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\AbstractKeyInfoType;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\MgmtData;
use SimpleSAML\XMLSecurity\XML\ds\PGPData;
use SimpleSAML\XMLSecurity\XML\ds\PGPKeyID;
use SimpleSAML\XMLSecurity\XML\ds\PGPKeyPacket;
use SimpleSAML\XMLSecurity\XML\ds\SPKIData;
use SimpleSAML\XMLSecurity\XML\ds\SPKISexp;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName;
use SimpleSAML\XMLSecurity\XML\xenc\P;
use SimpleSAML\XMLSecurity\XML\xenc\Seed;

use function dirname;
use function openssl_x509_parse;
use function str_replace;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\KeyInfoTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractKeyInfoType::class)]
#[CoversClass(KeyInfo::class)]
final class KeyInfoTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;


    /**
     */
    public function setUp(): void
    {
        self::$testedClass = KeyInfo::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_KeyInfo.xml',
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
        $SPKISexp1 = SPKISexp::fromString('GpM6');
        $seed = Seed::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $SPKISexp2 = SPKISexp::fromString('GpM7');
        $SPKISexp3 = SPKISexp::fromString('GpM8');
        $carriedKeyName = CarriedKeyName::fromString('Some label');

        $keyInfo = new KeyInfo(
            [
                KeyName::fromString('testkey'),
                new X509Data(
                    [
                        X509Certificate::fromString(self::$certificate),
                        X509SubjectName::fromString(self::$certData['name']),
                    ],
                ),
                new PGPData(
                    PGPKeyID::fromString('GpM7'),
                    PGPKeyPacket::fromString('GpM8'),
                    [
                        P::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                    ],
                ),
                new SPKIData([
                    [$SPKISexp1, $seed],
                    [$SPKISexp2, null],
                    [$SPKISexp3, $carriedKeyName],
                ]),
                MgmtData::fromString('ManagementData'),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed654'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($keyInfo),
        );
    }


    /**
     */
    public function testMarshallingEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ds:KeyInfo cannot be empty');

        new KeyInfo([]);
    }


    /**
     */
    public function testUnmarshallingEmpty(): void
    {
        $document = DOMDocumentFactory::fromString('<ds:KeyInfo xmlns:ds="' . KeyInfo::NS . '"/>');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ds:KeyInfo cannot be empty');

        KeyInfo::fromXML($document->documentElement);
    }
}
