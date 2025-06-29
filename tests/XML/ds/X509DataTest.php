<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\{AnyURIValue, Base64BinaryValue, IntegerValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, X509Certificate, X509Data};
use SimpleSAML\XMLSecurity\XML\ds\{X509IssuerName, X509IssuerSerial, X509SerialNumber, X509SubjectName};
use SimpleSAML\XMLSecurity\XML\dsig11\X509Digest;

use function base64_encode;
use function dirname;
use function hex2bin;
use function openssl_x509_parse;
use function str_replace;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\X509DataTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(X509Data::class)]
final class X509DataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var array<string, mixed> */
    private static array $certData;

    /** @var string */
    private static string $digest;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = X509Data::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509Data.xml',
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

        $key = new Key\X509Certificate(PEM::fromString(PEMCertificatesMock::getPlainCertificate()));
        /** @var string $binary */
        $binary = hex2bin($key->getRawThumbprint(C::DIGEST_SHA256));
        self::$digest = base64_encode($binary);
    }


    /**
     */
    public function testMarshalling(): void
    {
        $x509data = new X509Data(
            [
                new X509Certificate(
                    Base64BinaryValue::fromString(self::$certificate),
                ),
                new X509IssuerSerial(
                    new X509IssuerName(
                        StringValue::fromString(sprintf(
                            'C=%s,ST=%s,L=%s,O=%s,CN=%s,emailAddress=%s',
                            'US',
                            'Hawaii',
                            'Honolulu',
                            'SimpleSAMLphp HQ',
                            'SimpleSAMLphp Testing CA',
                            'noreply@simplesamlphp.org',
                        )),
                    ),
                    new X509SerialNumber(
                        IntegerValue::fromString('2'),
                    ),
                ),
                new X509SubjectName(
                    StringValue::fromString(self::$certData['name']),
                ),
                new X509Digest(
                    Base64BinaryValue::fromString(self::$digest),
                    AnyURIValue::fromString(C::DIGEST_SHA256),
                ),
            ],
            [
                new Chunk(
                    DOMDocumentFactory::fromString(
                        '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                    )->documentElement,
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">other</ssp:Chunk>',
                )->documentElement),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($x509data),
        );
    }
}
