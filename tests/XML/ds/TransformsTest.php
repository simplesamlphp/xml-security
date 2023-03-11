<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
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
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Transforms::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/ds_Transforms.xml',
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
        $ds_ns = Transforms::NS;
        $transforms = new Transforms([]);
        $this->assertEquals(
            "<ds:Transforms xmlns:ds=\"$ds_ns\"/>",
            strval($transforms),
        );
        $this->assertTrue($transforms->isEmptyElement());
    }
}
