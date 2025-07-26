<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\XML\ds\{Transform, Transforms, XPath};
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractXencElement, DataReference, KeyReference, ReferenceList};
use SimpleSAML\XPath\Constants as XPATH_C;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\ReferenceListTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(ReferenceList::class)]
final class ReferenceListTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = ReferenceList::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_ReferenceList.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $transformData = new Transform(
            AnyURIValue::fromString(XPATH_C::XPATH10_URI),
            new XPath(
                StringValue::fromString('self::xenc:EncryptedData[@Id="example1"]'),
            ),
        );
        $transformKey = new Transform(
            AnyURIValue::fromString(XPATH_C::XPATH10_URI),
            new XPath(
                StringValue::fromString('self::xenc:EncryptedKey[@Id="example1"]'),
            ),
        );

        $referenceList = new ReferenceList(
            [
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                    [new Transforms([$transformData])],
                ),
            ],
            [
                new KeyReference(
                    AnyURIValue::fromString('#Encrypted_KEY_ID'),
                    [new Transforms([$transformKey])],
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($referenceList),
        );
    }
}
