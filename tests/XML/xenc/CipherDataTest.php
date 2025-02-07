<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractXencElement, CipherData, CipherValue};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\CipherDataTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CipherData
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(CipherData::class)]
final class CipherDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = CipherData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_CipherData.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $cipherData = new CipherData(
            new CipherValue(
                Base64BinaryValue::fromString('c29tZSB0ZXh0'),
            ),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($cipherData),
        );
    }
}
