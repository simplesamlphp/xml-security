<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\DsObject;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\ds\ObjectTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Object
 *
 * @package simplesamlphp/xml-security
 */
final class ObjectTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = DsObject::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Object.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $obj = new DsObject(
            'abc123',
            'image/png',
            'http://www.w3.org/2000/09/xmldsig#base64',
            [new Chunk(DOMDocumentFactory::fromString('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=')->documentElement)]
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

        $this->assertEquals('abc123', $obj->getId());
        $this->assertEquals('image/png', $obj->getMimeType());
        $this->assertEquals('http://www.w3.org/2000/09/xmldsig#base64', $obj->getEncoding());
        $this->assertEquals('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=', $o->getElements()[0]->textContent);
    }
}
