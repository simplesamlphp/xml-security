<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, Base64BinaryValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, X509Digest};

use function base64_encode;
use function dirname;
use function hex2bin;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\X509DigestTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(X509Digest::class)]
final class X509DigestTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var string */
    private static string $digest;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = X509Digest::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_X509Digest.xml',
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
        $x509digest = new X509Digest(
            Base64BinaryValue::fromString(self::$digest),
            AnyURIValue::fromString(C::DIGEST_SHA256),
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($x509digest),
        );
    }
}
