<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\{Transform, Transforms, XPath};
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractReference, AbstractXencElement, DataReference};
use SimpleSAML\XPath\Constants as XPATH_C;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\DataReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\DataReference
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractReference::class)]
#[CoversClass(DataReference::class)]
final class DataReferenceTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DataReference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_DataReference.xml',
        );
    }

    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $dataReference = new DataReference(
            AnyURIValue::fromString('#Encrypted_DATA_ID'),
            [
                new Transforms(
                    [
                        new Transform(
                            AnyURIValue::fromString(XPATH_C::XPATH10_URI),
                            new XPath(
                                StringValue::fromString('self::xenc:EncryptedData[@Id="example1"]'),
                            ),
                        ),
                    ],
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($dataReference),
        );
    }
}
