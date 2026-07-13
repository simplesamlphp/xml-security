<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\Certificate as CertificateUtils;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerName;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerSerial;
use SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber;

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
        self::$issuer = X509IssuerName::fromString(CertificateUtils::parseIssuer($details['issuer']));
        self::$serial = X509SerialNumber::fromString($details['serialNumber']);
    }


    /**
     */
    public function testMarshalling(): void
    {
        $x509IssuerSerial = new X509IssuerSerial(self::$issuer, self::$serial);

        $expectedXml = self::$xmlRepresentation->saveXml(self::$xmlRepresentation->documentElement);
        $this->assertNotFalse($expectedXml);
        $actualXml = strval($x509IssuerSerial);

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $x509IssuerSerial = new X509IssuerSerial(self::$issuer, self::$serial);
        $x509IssuerSerialElement = $x509IssuerSerial->toXML();

        $xpCache = XPath::getXPath($x509IssuerSerialElement);

        $issuerName = XPath::xpQuery($x509IssuerSerialElement, './ds:X509IssuerName', $xpCache);
        $this->assertCount(1, $issuerName);

        /** @var \Dom\Element[] $x509IssuerSerialElements */
        $x509IssuerSerialElements = XPath::xpQuery(
            $x509IssuerSerialElement,
            './ds:X509IssuerName/following-sibling::*',
            $xpCache,
        );

        // Test ordering of X509IssuerSerial contents
        $this->assertCount(1, $x509IssuerSerialElements);
        $this->assertEquals('ds:X509SerialNumber', $x509IssuerSerialElements[0]->tagName);
    }
}
