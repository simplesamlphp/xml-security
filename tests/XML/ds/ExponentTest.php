<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\Exponent;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ExponentTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Exponent
 *
 * @package simplesamlphp/xml-security
 */
final class ExponentTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = Exponent::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Exponent.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $exponent = new Exponent('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo=');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content($this->xmlRepresentation),
            strval($exponent),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(SchemaViolationException::class);
        new Exponent('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $exponent = Exponent::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo=', $exponent->getContent());
    }
}
