<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\Test\XML\SchemaValidationTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\CarriedKeyNameTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName
 *
 * @package simplesamlphp/xml-security
 */
final class CarriedKeyNameTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = CarriedKeyName::class;

        $this->schema = dirname(dirname(dirname(dirname(__FILE__)))) . '/schemas/xenc-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_CarriedKeyName.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyName = new CarriedKeyName('Some label');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($keyName),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyName = CarriedKeyName::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('Some label', $keyName->getContent());
    }
}
