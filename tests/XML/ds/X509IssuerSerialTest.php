<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\Utils\Certificate as CertificateUtils;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerSerial;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerName;
use SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509IssuerSerial
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509IssuerSerial
 *
 * @package simplesamlphp/xml-security
 */
final class X509IssuerSerialTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    private Key\X509Certificate $key;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\X509IssuerName */
    private X509IssuerName $issuer;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber */
    private X509SerialNumber $serial;


    /**
     */
    public function setUp(): void
    {
        $this->key = new Key\X509Certificate(
            PEMCertificatesMock::getPlainPublicKey()
        );

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509IssuerSerial.xml'
        );

        $details = $this->key->getCertificateDetails();
        $this->issuer = new X509IssuerName(CertificateUtils::parseIssuer($details['issuer']));
        $this->serial = new X509SerialNumber($details['serialNumber']);
    }


    /**
     */
    public function testMarshalling(): void
    {
        $X509IssuerSerial = new X509IssuerSerial($this->issuer, $this->serial);

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($X509IssuerSerial)
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $X509IssuerSerial = new X509IssuerSerial($this->issuer, $this->serial);
        $X509IssuerSerialElement = $X509IssuerSerial->toXML();

        $issuerName = XMLUtils::xpQuery($X509IssuerSerialElement, './ds:X509IssuerName');
        $this->assertCount(1, $issuerName);

        /** @psalm-var \DOMElement[] $X509IssuerSerialElements */
        $X509IssuerSerialElements = XMLUtils::xpQuery($X509IssuerSerialElement, './ds:X509IssuerName/following-sibling::*');

        // Test ordering of X509IssuerSerial contents
        $this->assertCount(1, $X509IssuerSerialElements);
        $this->assertEquals('ds:X509SerialNumber', $X509IssuerSerialElements[0]->tagName);
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $X509IssuerSerial = X509IssuerSerial::fromXML($this->document->documentElement);

        $this->assertEquals($this->issuer, $X509IssuerSerial->getIssuerName());
        $this->assertEquals($this->serial, $X509IssuerSerial->getSerialNumber());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(X509IssuerSerial::fromXML($this->document->documentElement))))
        );
    }
}
