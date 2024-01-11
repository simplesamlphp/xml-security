<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\X509SerialNumber;

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
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = X509SerialNumber::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509SerialNumber.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $serialNumber = new X509SerialNumber('123456');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($serialNumber),
        );
    }


    /**
     */
    public function testUnmarshallingIncorrectTypeThrowsException(): void
    {
        $document = clone self::$xmlRepresentation;
        $document->documentElement->textContent = 'Not an integer';

        $this->expectException(SchemaViolationException::class);
        X509SerialNumber::fromXML($document->documentElement);
    }
}
