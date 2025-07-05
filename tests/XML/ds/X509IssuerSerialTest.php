<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\{IntegerValue, StringValue};
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\{Certificate as CertificateUtils, XPath};
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, X509IssuerName, X509IssuerSerial, X509SerialNumber};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509IssuerSerial
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(X509IssuerSerial::class)]
final class X509IssuerSerialTest extends TestCase
{
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    private static Key\X509Certificate $key;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\X509IssuerName */
    private static X509IssuerName $issuer;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber */
    private static X509SerialNumber $serial;


    /**
     */
    public function setUp(): void
    {
        self::$testedClass = X509IssuerSerial::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509IssuerSerial.xml',
        );

        self::$key = new Key\X509Certificate(PEM::fromString(PEMCertificatesMock::getPlainCertificate()));

        /** @var string[] $details */
        $details = self::$key->getCertificateDetails();
        self::$issuer = new X509IssuerName(
            StringValue::fromString(CertificateUtils::parseIssuer($details['issuer'])),
        );
        self::$serial = new X509SerialNumber(
            IntegerValue::fromString($details['serialNumber']),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $X509IssuerSerial = new X509IssuerSerial(self::$issuer, self::$serial);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($X509IssuerSerial),
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $X509IssuerSerial = new X509IssuerSerial(self::$issuer, self::$serial);
        $X509IssuerSerialElement = $X509IssuerSerial->toXML();

        $xpCache = XPath::getXPath($X509IssuerSerialElement);

        $issuerName = XPath::xpQuery($X509IssuerSerialElement, './ds:X509IssuerName', $xpCache);
        $this->assertCount(1, $issuerName);

        /** @var \DOMElement[] $X509IssuerSerialElements */
        $X509IssuerSerialElements = XPath::xpQuery(
            $X509IssuerSerialElement,
            './ds:X509IssuerName/following-sibling::*',
            $xpCache,
        );

        // Test ordering of X509IssuerSerial contents
        $this->assertCount(1, $X509IssuerSerialElements);
        $this->assertEquals('ds:X509SerialNumber', $X509IssuerSerialElements[0]->tagName);
    }
}
