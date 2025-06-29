<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\{AnyURIValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, DsObject};

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\ds\ObjectTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(DsObject::class)]
final class ObjectTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DsObject::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Object.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $img = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        $obj = new DsObject(
            IDValue::fromString('abc123'),
            StringValue::fromString('image/png'),
            AnyURIValue::fromString('http://www.w3.org/2000/09/xmldsig#base64'),
            [
                new Chunk(
                    DOMDocumentFactory::fromString(sprintf(
                        '<ssp:data xmlns:ssp="urn:ssp:custom">%s</ssp:data>',
                        $img,
                    ))->documentElement,
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($obj),
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
            strval($obj),
        );
        $this->assertTrue($obj->isEmptyElement());
    }
}
