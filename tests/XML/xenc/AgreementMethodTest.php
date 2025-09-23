<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractAgreementMethodType;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement;
use SimpleSAML\XMLSecurity\XML\xenc\AgreementMethod;
use SimpleSAML\XMLSecurity\XML\xenc\KANonce;
use SimpleSAML\XMLSecurity\XML\xenc\OriginatorKeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\RecipientKeyInfo;

use function dirname;
use function openssl_x509_parse;
use function str_replace;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\AgreementMethodTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractAgreementMethodType::class)]
#[CoversClass(AgreementMethod::class)]
final class AgreementMethodTest extends TestCase
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
        self::$testedClass = AgreementMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_AgreementMethod.xml',
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
        $kaNonce = new KANonce(
            Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );

        $digestMethod = new DigestMethod(
            AnyURIValue::fromString(C::DIGEST_SHA256),
            [
                new Chunk(DOMDocumentFactory::fromString(
                    '<some:Chunk xmlns:some="urn:x-simplesamlphp:namespace">some</some:Chunk>',
                )->documentElement),
            ],
        );

        $originatorKeyInfo = new OriginatorKeyInfo(
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
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">originator</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed123'),
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
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">recipient</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed654'),
        );

        $agreementMethod = new AgreementMethod(
            AnyURIValue::fromString(C::KEY_AGREEMENT_ECDH_ES),
            $kaNonce,
            $originatorKeyInfo,
            $recipientKeyInfo,
            [$digestMethod],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($agreementMethod),
        );
    }


    public function testMarshallingElementOrdering(): void
    {
        $kaNonce = new KANonce(
            Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );

        $digestMethod = new DigestMethod(
            AnyURIValue::fromString(C::DIGEST_SHA256),
            [
                new Chunk(DOMDocumentFactory::fromString(
                    '<some:Chunk xmlns:some="urn:x-simplesamlphp:namespace">some</some:Chunk>',
                )->documentElement),
            ],
        );

        $originatorKeyInfo = new OriginatorKeyInfo(
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
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">originator</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed321'),
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
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">recipient</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed654'),
        );

        $agreementMethod = new AgreementMethod(
            AnyURIValue::fromString(C::KEY_AGREEMENT_ECDH_ES),
            $kaNonce,
            $originatorKeyInfo,
            $recipientKeyInfo,
            [$digestMethod],
        );

        // Marshall it to a \DOMElement
        $agreementMethodElement = $agreementMethod->toXML();

        $xpCache = XPath::getXPath($agreementMethodElement);

        // Test for an KA-Nonce
        /** @var \DOMElement[] $kaNonceElements */
        $kaNonceElements = XPath::xpQuery($agreementMethodElement, './xenc:KA-Nonce', $xpCache);
        $this->assertCount(1, $kaNonceElements);

        // Test ordering of AgreementMethod contents
        /** @var \DOMElement[] $agreementMethodElements */
        $agreementMethodElements = XPath::xpQuery(
            $agreementMethodElement,
            './xenc:KA-Nonce/following-sibling::*',
            $xpCache,
        );

        $this->assertCount(3, $agreementMethodElements);
        $this->assertEquals('ds:DigestMethod', $agreementMethodElements[0]->tagName);
        $this->assertEquals('xenc:OriginatorKeyInfo', $agreementMethodElements[1]->tagName);
        $this->assertEquals('xenc:RecipientKeyInfo', $agreementMethodElements[2]->tagName);
    }
}
