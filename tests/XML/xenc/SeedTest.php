<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractXencElement, Seed};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\SeedTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\Seed
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(Seed::class)]
final class SeedTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Seed::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_seed.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $seed = new Seed(
            CryptoBinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($seed),
        );
    }
}
