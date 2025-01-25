<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\Type\DigestValueValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, DigestValue};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\DigestValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(DigestValue::class)]
final class DigestValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DigestValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_DigestValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $digestValue = new DigestValue(
            DigestValueValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($digestValue),
        );
    }
}
