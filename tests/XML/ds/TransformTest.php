<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\XPath;

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
            C::XPATH_URI,
            new XPath('count(//. | //@* | //namespace::*)')
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
        $this->assertEquals(C::XPATH_URI, $transform->getAlgorithm());

        $xpath = $transform->getXPath();

        $this->assertInstanceOf(XPath::class, $xpath);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transform)
        );
    }
}
