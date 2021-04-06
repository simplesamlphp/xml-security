<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\Constants;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

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
                    'http://www.w3.org/TR/1999/REC-xpath-19991116',
                    [
                        new Chunk(
                            DOMDocumentFactory::fromString(
                                '<ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">self::xenc:CipherValue[@Id="example1"]</ds:XPath>'
                            )->documentElement
                        )
                    ]
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
        $this->assertEquals('http://www.w3.org/TR/1999/REC-xpath-19991116', $transform->getAlgorithm());

        $elements = $transform->getElements();
        $this->assertCount(1, $elements);

        $this->assertInstanceOf(Chunk::class, $elements[0]);

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
