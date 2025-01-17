<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\xenc11\{AbstractKeyDerivationMethodType, AbstractXenc11Element, KeyDerivationMethod};

use function dirname;
use function strval;

/**
 * Tests for the xenc:KeyDerivationMethod element.
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\AbstractKeyDerivationMethodType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\KeyDerivationMethod
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(AbstractKeyDerivationMethodType::class)]
#[CoversClass(KeyDerivationMethod::class)]
final class KeyDerivationMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = KeyDerivationMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_KeyDerivationMethod.xml',
        );
    }


    // test marshalling


    /**
     * Test creating an KeyDerivationMethod object from scratch.
     */
    public function testMarshalling(): void
    {
        $kdm = new KeyDerivationMethod(
            AnyURIValue::fromString(C::KEY_DERIVATION_CONCATKDF),
            [
                new KeyName(StringValue::fromString('testkey')),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($kdm),
        );
    }
}
