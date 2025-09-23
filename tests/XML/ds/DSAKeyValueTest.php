<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDSAKeyValueType;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\DSAKeyValue;
use SimpleSAML\XMLSecurity\XML\ds\G;
use SimpleSAML\XMLSecurity\XML\ds\J;
use SimpleSAML\XMLSecurity\XML\ds\P;
use SimpleSAML\XMLSecurity\XML\ds\PgenCounter;
use SimpleSAML\XMLSecurity\XML\ds\Q;
use SimpleSAML\XMLSecurity\XML\ds\Seed;
use SimpleSAML\XMLSecurity\XML\ds\Y;

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
            CryptoBinaryValue::fromString('GpM1'),
        );
        $q = new Q(
            CryptoBinaryValue::fromString('GpM2'),
        );
        $g = new G(
            CryptoBinaryValue::fromString('GpM3'),
        );
        $y = new Y(
            CryptoBinaryValue::fromString('GpM4'),
        );
        $j = new J(
            CryptoBinaryValue::fromString('GpM5'),
        );
        $seed = new Seed(
            CryptoBinaryValue::fromString('GpM6'),
        );
        $pgenCounter = new PgenCounter(
            CryptoBinaryValue::fromString('GpM7'),
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
            CryptoBinaryValue::fromString('GpM1'),
        );
        $q = new Q(
            CryptoBinaryValue::fromString('GpM2'),
        );
        $g = new G(
            CryptoBinaryValue::fromString('GpM3'),
        );
        $y = new Y(
            CryptoBinaryValue::fromString('GpM4'),
        );
        $j = new J(
            CryptoBinaryValue::fromString('GpM5'),
        );
        $seed = new Seed(
            CryptoBinaryValue::fromString('GpM6'),
        );
        $pgenCounter = new PgenCounter(
            CryptoBinaryValue::fromString('GpM7'),
        );

        $dsaKeyValue = new DSAKeyValue($y, $g, $j, $p, $q, $seed, $pgenCounter);

        $dsaKeyValueElement = $dsaKeyValue->toXML();
        /** @var \DOMNodeList<\DOMNode> $children */
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
