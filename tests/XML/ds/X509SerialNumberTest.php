<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableElementTestTrait;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509SerialNumberTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber
 *
 * @package simplesamlphp/xml-security
 */
final class X509SerialNumberTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = X509SerialNumber::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509SerialNumber.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $serialNumber = new X509SerialNumber('123456');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($serialNumber),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $serialNumber = X509SerialNumber::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($serialNumber),
        );
    }


    /**
     */
    public function testUnmarshallingIncorrectTypeThrowsException(): void
    {
        $document = $this->xmlRepresentation;
        $document->documentElement->textContent = 'Not an integer';

        $this->expectException(SchemaViolationException::class);
        X509SerialNumber::fromXML($this->xmlRepresentation->documentElement);
    }
}
