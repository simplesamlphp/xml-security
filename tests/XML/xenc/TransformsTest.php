<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\Transforms;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\TransformsTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\Transforms
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 *
 * @package simplesamlphp/xml-security
 */
final class TransformsTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Transforms::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/xenc_Transforms.xml',
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
                        'count(//. | //@* | //namespace::*)',
                    ),
                ),
            ],
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transforms),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $transforms = Transforms::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($transforms),
        );
    }


    /**
     * Adding an empty Transforms element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $xenc_ns = Transforms::NS;
        $transforms = new Transforms([]);
        $this->assertEquals(
            "<xenc:Transforms xmlns:xenc=\"$xenc_ns\"/>",
            strval($transforms),
        );
        $this->assertTrue($transforms->isEmptyElement());
    }
}
