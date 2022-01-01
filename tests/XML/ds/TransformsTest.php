<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\ds\XPath;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\TransformsTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Transforms
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/xml-security
 */
final class TransformsTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Transforms::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_Transforms.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transforms = new Transforms(
            [
                new Transform(
                    C::XPATH_URI,
                    new XPath(
                        'self::xenc:CipherValue[@Id="example1"]',
                        ['xenc' => 'http://www.w3.org/2001/04/xmlenc#']
                    ),
                )
            ]
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transforms)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $transforms = Transforms::fromXML($this->xmlRepresentation->documentElement);
        $transform = $transforms->getTransform();
        $this->assertCount(1, $transform);

        $transform = array_pop($transform);
        $this->assertEquals(C::XPATH_URI, $transform->getAlgorithm());

        $xpath = $transform->getXPath();
        $this->assertInstanceOf(XPath::class, $xpath);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transforms)
        );
    }


    /**
     * Adding an empty Transforms element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $ds_ns = Transforms::NS;
        $transforms = new Transforms([]);
        $this->assertEquals(
            "<ds:Transforms xmlns:ds=\"$ds_ns\"/>",
            strval($transforms)
        );
        $this->assertTrue($transforms->isEmptyElement());
    }
}
