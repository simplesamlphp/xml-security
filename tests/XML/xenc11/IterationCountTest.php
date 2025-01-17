<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element;
use SimpleSAML\XMLSecurity\XML\xenc11\IterationCount;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\xenc11\IterationCountTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(IterationCount::class)]
final class IterationCountTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = IterationCount::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_IterationCount.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $iterationCount = new IterationCount(3);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($iterationCount),
        );
    }
}