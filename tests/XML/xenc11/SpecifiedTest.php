<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XMLSecurity\XML\xenc11\{AbstractXenc11Element, Specified};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\xenc11\SpecifiedTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(Specified::class)]
final class SpecifiedTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Specified::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_Specified.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $specified = new Specified(
            Base64BinaryValue::fromString('GpM6'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($specified),
        );
    }
}
