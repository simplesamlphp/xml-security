<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\{Chunk, Constants as C, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\XML\xenc\{
    AbstractEncryptionPropertiesType,
    AbstractXencElement,
    EncryptionProperties,
    EncryptionProperty,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptionPropertiesTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptionPropertiesType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperties
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractEncryptionPropertiesType::class)]
#[CoversClass(EncryptionProperties::class)]
final class EncryptionPropertiesTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptionProperties::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptionProperties.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $someDoc = DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        );
        $otherDoc = DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Other</ssp:Chunk>',
        );

        /** @var \DOMElement $someElt */
        $someElt = $someDoc->documentElement;
        /** @var \DOMElement $otherElt */
        $otherElt = $otherDoc->documentElement;

        $attr1 = new XMLAttribute(
            C::NS_XML,
            'xml',
            'lang',
            StringValue::fromString('en'),
        );
        $attr2 = new XMLAttribute(
            C::NS_XML,
            'xml',
            'lang',
            StringValue::fromString('nl'),
        );

        $encryptionProperty1 = new EncryptionProperty(
            [new Chunk($someElt)],
            AnyURIValue::fromString('urn:x-simplesamlphp:phpunit'),
            IDValue::fromString('inner-first'),
            [$attr1],
        );
        $encryptionProperty2 = new EncryptionProperty(
            [new Chunk($otherElt)],
            AnyURIValue::fromString('urn:x-simplesamlphp:phpunit'),
            IDValue::fromString('inner-second'),
            [$attr2],
        );

        $encryptionProperties = new EncryptionProperties(
            [$encryptionProperty1, $encryptionProperty2],
            IDValue::fromString('outer'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptionProperties),
        );
    }
}
