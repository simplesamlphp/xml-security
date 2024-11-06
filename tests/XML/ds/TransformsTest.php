<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\ds\XPath;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\TransformsTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(Transforms::class)]
final class TransformsTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Transforms::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Transforms.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transforms = new Transforms(
            [
                new Transform(
                    C::XPATH10_URI,
                    new XPath(
                        'count(//. | //@* | //namespace::*)',
                    ),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
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
