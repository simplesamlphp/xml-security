<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractCharTwoFieldParamsType;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractTnBFieldParamsType;
use SimpleSAML\XMLSecurity\XML\dsig11\K;
use SimpleSAML\XMLSecurity\XML\dsig11\M;
use SimpleSAML\XMLSecurity\XML\dsig11\TnB;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\TnBTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractCharTwoFieldParamsType::class)]
#[CoversClass(AbstractTnBFieldParamsType::class)]
#[CoversClass(K::class)]
#[CoversClass(M::class)]
#[CoversClass(TnB::class)]
final class TnBTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = TnB::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_TnB.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $m = new M(1024);
        $k = new K(64);
        $tnb = new TnB($m, $k);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($tnb),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $m = new M(1024);
        $k = new K(64);
        $tnb = new TnB($m, $k);

        $tnbElement = $tnb->toXML();
        /** @var \DOMElement[] $children */
        $children = $tnbElement->childNodes;

        $this->assertEquals('dsig11:M', $children[0]->tagName);
        $this->assertEquals('dsig11:K', $children[1]->tagName);
    }
}
