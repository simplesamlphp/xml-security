<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Attribute as XMLAttribute, Chunk, Constants as C, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractEncryptionPropertyType, AbstractXencElement, EncryptionProperty};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptionPropertyTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptionPropertyType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractEncryptionPropertyType::class)]
#[CoversClass(EncryptionProperty::class)]
final class EncryptionPropertyTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptionProperty::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptionProperty.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $doc = DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        );
        /** @var \DOMElement $elt */
        $elt = $doc->documentElement;

        $attr = new XMLAttribute(
            C::NS_XML,
            'xml',
            'lang',
            StringValue::fromString('en'),
        );

        $encryptionProperty = new EncryptionProperty(
            [new Chunk($elt)],
            AnyURIValue::fromString('urn:x-simplesamlphp:phpunit'),
            IDValue::fromString('phpunit'),
            [$attr],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptionProperty),
        );
    }
}
