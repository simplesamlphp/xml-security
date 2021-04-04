<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerName;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509IssuerNameTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509IssuerName
 *
 * @package simplesamlphp/xml-security
 */
final class X509IssuerNameTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = X509IssuerName::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509IssuerName.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $issuerName = new X509IssuerName('some name');

        $issuerNameElement = $issuerName->toXML();
        $this->assertEquals('some name', $issuerNameElement->textContent);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($issuerName)
        );
    }


    /**
     */
    public function testMarshallingEmptyThrowsException(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('ds:X509IssuerName cannot be empty.');

        new X509IssuerName('');
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $issuerName = X509IssuerName::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('some name', $issuerName->getName());
    }
}
