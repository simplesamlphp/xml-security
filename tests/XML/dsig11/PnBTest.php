<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\PositiveIntegerValue;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractCharTwoFieldParamsType;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractPnBFieldParamsType;
use SimpleSAML\XMLSecurity\XML\dsig11\K1;
use SimpleSAML\XMLSecurity\XML\dsig11\K2;
use SimpleSAML\XMLSecurity\XML\dsig11\K3;
use SimpleSAML\XMLSecurity\XML\dsig11\M;
use SimpleSAML\XMLSecurity\XML\dsig11\PnB;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\PnBTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractCharTwoFieldParamsType::class)]
#[CoversClass(AbstractPnBFieldParamsType::class)]
#[CoversClass(K1::class)]
#[CoversClass(K2::class)]
#[CoversClass(K3::class)]
#[CoversClass(M::class)]
#[CoversClass(PnB::class)]
final class PnBTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = PnB::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_PnB.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $m = new M(PositiveIntegerValue::fromInteger(1024));
        $k1 = new K1(PositiveIntegerValue::fromInteger(128));
        $k2 = new K2(PositiveIntegerValue::fromInteger(256));
        $k3 = new K3(PositiveIntegerValue::fromInteger(512));
        $pnb = new PnB($m, $k1, $k2, $k3);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($pnb),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $m = new M(PositiveIntegerValue::fromInteger(1024));
        $k1 = new K1(PositiveIntegerValue::fromInteger(128));
        $k2 = new K2(PositiveIntegerValue::fromInteger(256));
        $k3 = new K3(PositiveIntegerValue::fromInteger(512));
        $pnb = new PnB($m, $k1, $k2, $k3);

        $pnbElement = $pnb->toXML();
        /** @var \DOMNodeList<\DOMNode> $children */
        $children = $pnbElement->childNodes;

        $this->assertEquals('dsig11:M', $children[0]->tagName);
        $this->assertEquals('dsig11:K1', $children[1]->tagName);
        $this->assertEquals('dsig11:K2', $children[2]->tagName);
        $this->assertEquals('dsig11:K3', $children[3]->tagName);
    }
}
