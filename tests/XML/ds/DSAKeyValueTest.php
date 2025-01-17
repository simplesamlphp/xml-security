<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDSAKeyValueType, AbstractDsElement, DSAKeyValue};
use SimpleSAML\XMLSecurity\XML\ds\{G, J, P, PgenCounter, Q, Seed, Y};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\DSAKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractDSAKeyValueType::class)]
#[CoversClass(DSAKeyValue::class)]
final class DSAKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DSAKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_DSAKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $p = new P(
            Base64BinaryValue::fromString('GpM1'),
        );
        $q = new Q(
            Base64BinaryValue::fromString('GpM2'),
        );
        $g = new G(
            Base64BinaryValue::fromString('GpM3'),
        );
        $y = new Y(
            Base64BinaryValue::fromString('GpM4'),
        );
        $j = new J(
            Base64BinaryValue::fromString('GpM5'),
        );
        $seed = new Seed(
            Base64BinaryValue::fromString('GpM6'),
        );
        $pgenCounter = new PgenCounter(
            Base64BinaryValue::fromString('GpM7'),
        );

        $dsaKeyValue = new DSAKeyValue($y, $g, $j, $p, $q, $seed, $pgenCounter);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($dsaKeyValue),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $p = new P(
            Base64BinaryValue::fromString('GpM1'),
        );
        $q = new Q(
            Base64BinaryValue::fromString('GpM2'),
        );
        $g = new G(
            Base64BinaryValue::fromString('GpM3'),
        );
        $y = new Y(
            Base64BinaryValue::fromString('GpM4'),
        );
        $j = new J(
            Base64BinaryValue::fromString('GpM5'),
        );
        $seed = new Seed(
            Base64BinaryValue::fromString('GpM6'),
        );
        $pgenCounter = new PgenCounter(
            Base64BinaryValue::fromString('GpM7'),
        );

        $dsaKeyValue = new DSAKeyValue($y, $g, $j, $p, $q, $seed, $pgenCounter);

        $dsaKeyValueElement = $dsaKeyValue->toXML();
        /** @var \DOMElement[] $children */
        $children = $dsaKeyValueElement->childNodes;

        $this->assertEquals('ds:P', $children[0]->tagName);
        $this->assertEquals('ds:Q', $children[1]->tagName);
        $this->assertEquals('ds:G', $children[2]->tagName);
        $this->assertEquals('ds:Y', $children[3]->tagName);
        $this->assertEquals('ds:J', $children[4]->tagName);
        $this->assertEquals('ds:Seed', $children[5]->tagName);
        $this->assertEquals('ds:PgenCounter', $children[6]->tagName);
    }
}
