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
                new Chunk(DOMDocumentFactory::fromString('<ds:XPath>count(//. | //@* | //namespace::*)</ds:XPath>')->documentElement)
            ],
        );

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
        $this->assertCount(1, $elements);

        $this->assertInstanceOf(Chunk::class, $elements[0]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transform)
        );
    }
}
