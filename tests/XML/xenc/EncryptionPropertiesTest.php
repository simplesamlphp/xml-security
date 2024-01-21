<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperties;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty;

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
final class EncryptionPropertiesTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptionProperties::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema.xsd';

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

        $attr1 = new XMLAttribute(C::NS_XML, 'xml', 'lang', 'en');
        $attr2 = new XMLAttribute(C::NS_XML, 'xml', 'lang', 'nl');

        $encryptionProperty1 = new EncryptionProperty(
            [new Chunk($someElt)],
            'urn:x-simplesamlphp:phpunit',
            'inner-first',
            [$attr1],
        );
        $encryptionProperty2 = new EncryptionProperty(
            [new Chunk($otherElt)],
            'urn:x-simplesamlphp:phpunit',
            'inner-second',
            [$attr2],
        );

        $encryptionProperties = new EncryptionProperties([$encryptionProperty1, $encryptionProperty2], 'outer');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptionProperties),
        );
    }
}
