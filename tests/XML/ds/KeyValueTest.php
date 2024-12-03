<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\KeyValue;
use SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\KeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(KeyValue::class)]
final class KeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var \DOMDocument $empty */
    protected static DOMDocument $empty;

    /** @var \DOMDocument $rsaKeyValue */
    protected static DOMDocument $rsaKeyValue;

    /** @var \DOMDocument $encryptionProperty */
    protected static DOMDocument $encryptionProperty;


    /**
     */
    protected function setUp(): void
    {
        self::$testedClass = KeyValue::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        self::$empty = DOMDocumentFactory::fromString('<ds:KeyValue xmlns:ds="http://www.w3.org/2000/09/xmldsig#"/>');

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_KeyValue.xml',
        );

        self::$rsaKeyValue = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_RSAKeyValue.xml',
        );

        self::$encryptionProperty = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptionProperty.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyValue = new KeyValue(RSAKeyValue::fromXML(self::$rsaKeyValue->documentElement));

        $rsaKeyValue = $keyValue->getRSAKeyValue();
        $this->assertInstanceOf(RSAKeyValue::class, $rsaKeyValue);
        $this->assertEmpty($keyValue->getElements());

        $this->assertEquals($rsaKeyValue->getModulus()->getContent(), 'dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg==');
        $this->assertEquals($rsaKeyValue->getExponent()->getContent(), 'dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo=');

        $document = self::$empty;
        $document->documentElement->appendChild($document->importNode(self::$rsaKeyValue->documentElement, true));

        $this->assertXmlStringEqualsXmlString($document->saveXML($document->documentElement), strval($keyValue));
    }


    /**
     */
    public function testMarshallingWithOtherElement(): void
    {
        $keyValue = new KeyValue(null, EncryptionProperty::fromXML(self::$encryptionProperty->documentElement));

        $elements = $keyValue->getElements();
        $this->assertEmpty($keyValue->getRSAKeyValue());
        $this->assertCount(1, $elements);

        $element = reset($elements);
        $this->assertInstanceOf(EncryptionProperty::class, $element);

        $document = self::$empty;
        $element->toXML($document->documentElement);

        $this->assertXmlStringEqualsXmlString($document->saveXML($document->documentElement), strval($keyValue));
    }


    /**
     */
    public function testMarshallingEmpty(): void
    {
        $this->expectException(SchemaViolationException::class);
        $this->expectExceptionMessage(
            'A <ds:KeyValue> requires either a RSAKeyValue or an element in namespace ##other',
        );

        new KeyValue(null, null);
    }


    /**
     */
    public function testUnmarshallingWithOtherElement(): void
    {
        $document = self::$empty;
        $document->documentElement->appendChild(
            $document->importNode(self::$encryptionProperty->documentElement, true),
        );

        $keyValue = KeyValue::fromXML($document->documentElement);

        $elements = $keyValue->getElements();
        $this->assertNull($keyValue->getRSAKeyValue());
        $this->assertCount(1, $elements);

        $element = reset($elements);
        $this->assertInstanceOf(EncryptionProperty::class, $element);
    }


    /**
     */
    public function testUnmarshallingEmpty(): void
    {
        $document = self::$empty;

        $this->expectException(SchemaViolationException::class);
        $this->expectExceptionMessage(
            'A <ds:KeyValue> requires either a RSAKeyValue or an element in namespace ##other',
        );

        KeyValue::fromXML($document->documentElement);
    }
}
