<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Constants;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\Transform;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\TransformTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Transform
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/xml-security
 */
final class TransformTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Transform::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_Transform.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transform = new Transform(
            'http://www.w3.org/TR/1999/REC-xpath-19991116',
            [
                new Chunk(DOMDocumentFactory::fromString('<some:Chunk>Random</some:Chunk>')->documentElement),
                new Chunk(DOMDocumentFactory::fromString('<ds:XPath>count(//. | //@* | //namespace::*)</ds:XPath>')->documentElement)
            ],
        );

        $document = DOMDocumentFactory::fromString('<root />');
        /** @psalm-var \DOMElement $document->firstChild */
        $transformElement = $transform->toXML($document->firstChild);

        $this->assertEquals('http://www.w3.org/TR/1999/REC-xpath-19991116', $transformElement->getAttribute('Algorithm'));
        $this->assertCount(2, $transformElement->childNodes);

        $this->assertEquals('some:Chunk', $transformElement->childNodes[0]->localName);
        $this->assertEquals('Random', $transformElement->childNodes[0]->textContent);
        $this->assertEquals('ds:XPath', $transformElement->childNodes[1]->localName);
        $this->assertEquals('count(//. | //@* | //namespace::*)', $transformElement->childNodes[1]->textContent);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transform)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $transform = Transform::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('http://www.w3.org/TR/1999/REC-xpath-19991116', $transform->getAlgorithm());

        $elements = $transform->getElements();
        $this->assertCount(2, $elements);

        $this->assertInstanceOf(Chunk::class, $elements[0]);
        $this->assertInstanceOf(Chunk::class, $elements[1]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transform)
        );
    }


    /**
     * Adding an empty Transform element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $ds_ns = Transform::NS;
        $transform = new Transform('http://www.w3.org/TR/1999/REC-xpath-19991116', []);
        $this->assertEquals(
            "<ds:Transform xmlns:ds=\"$ds_ns\" Algorithm=\"http://www.w3.org/TR/1999/REC-xpath-19991116\"/>",
            strval($transform)
        );
        $this->assertTrue($transform->isEmptyElement());
    }
}
