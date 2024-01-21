<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty;

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
final class EncryptionPropertyTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptionProperty::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema.xsd';

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

        $attr = new XMLAttribute(C::NS_XML, 'xml', 'lang', 'en');
        $encryptionProperty = new EncryptionProperty(
            [new Chunk($elt)],
            'urn:x-simplesamlphp:phpunit',
            'phpunit',
            [$attr],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptionProperty),
        );
    }
}
