<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\PositiveIntegerValue;
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, M};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\MTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(M::class)]
final class MTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = M::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_M.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $m = new M(PositiveIntegerValue::fromInteger(1024));

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($m),
        );
    }
}
