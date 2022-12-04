<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SchemaValidationTestTrait;
use SimpleSAML\Test\XML\SerializableElementTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\DsObject;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\ds\ObjectTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\DsObject
 *
 * @package simplesamlphp/xml-security
 */
final class ObjectTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = DsObject::class;

        $this->schema = dirname(dirname(dirname(dirname(__FILE__)))) . '/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Object.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $img = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        $obj = new DsObject(
            'abc123',
            'image/png',
            'http://www.w3.org/2000/09/xmldsig#base64',
            [
                new Chunk(
                    DOMDocumentFactory::fromString(sprintf(
                        '<ssp:data xmlns:ssp="urn:ssp:custom">%s</ssp:data>',
                        $img
                    ))->documentElement
                )
            ],
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($obj)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $obj = DsObject::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($obj)
        );
    }


    /**
     * Adding an empty Object-element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $ds_ns = DsObject::NS;
        $obj = new DsObject(null, null, null, []);
        $this->assertEquals(
            "<ds:Object xmlns:ds=\"$ds_ns\"/>",
            strval($obj)
        );
        $this->assertTrue($obj->isEmptyElement());
    }
}
