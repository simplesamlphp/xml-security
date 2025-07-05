<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractCurveType, AbstractDsig11Element};
use SimpleSAML\XMLSecurity\XML\dsig11\{A, B, Curve};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\CurveTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractCurveType::class)]
#[CoversClass(Curve::class)]
final class CurveTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Curve::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_Curve.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $a = new A(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $b = new B(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $curve = new Curve($a, $b);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($curve),
        );
    }
}
