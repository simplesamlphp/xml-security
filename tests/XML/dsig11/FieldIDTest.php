<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractFieldIDType;
use SimpleSAML\XMLSecurity\XML\dsig11\FieldID;
use SimpleSAML\XMLSecurity\XML\dsig11\GnB;
use SimpleSAML\XMLSecurity\XML\dsig11\K;
use SimpleSAML\XMLSecurity\XML\dsig11\K1;
use SimpleSAML\XMLSecurity\XML\dsig11\K2;
use SimpleSAML\XMLSecurity\XML\dsig11\K3;
use SimpleSAML\XMLSecurity\XML\dsig11\M;
use SimpleSAML\XMLSecurity\XML\dsig11\P;
use SimpleSAML\XMLSecurity\XML\dsig11\PnB;
use SimpleSAML\XMLSecurity\XML\dsig11\Prime;
use SimpleSAML\XMLSecurity\XML\dsig11\TnB;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\FieldIDTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractFieldIDType::class)]
#[CoversClass(FieldID::class)]
final class FieldIDTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = FieldID::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_FieldID.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $p = new P('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');
        $prime = new Prime($p);

        $m = new M(1024);
        $k = new K(64);
        $tnb = new TnB($m, $k);

        $k1 = new K1(128);
        $k2 = new K2(256);
        $k3 = new K3(512);
        $pnb = new PnB($m, $k1, $k2, $k3);

        $gnb = new GnB($m);

        $chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
        )->documentElement);

        $fieldId = new FieldID($prime, $tnb, $pnb, $gnb, [$chunk]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($fieldId),
        );
    }
}
