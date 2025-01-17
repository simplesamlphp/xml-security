<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\{
    AbstractDHKeyValueType,
    AbstractXencElement,
    DHKeyValue,
    Generator,
    P,
    PgenCounter,
    Q,
    Seed,
    XencPublic,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\DHKeyValueTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractDHKeyValueType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\DHKeyValue
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractDHKeyValueType::class)]
#[CoversClass(DHKeyValue::class)]
final class DHKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    private static DHKeyValue $dhKeyValue;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DHKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_DHKeyValue.xml',
        );

        self::$dhKeyValue = new DHKeyValue(
            new XencPublic(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new P(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new Q(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new Generator(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new Seed(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new PgenCounter(
                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval(self::$dhKeyValue),
        );
    }
    /**
     */
    public function testMarshallingElementOrder(): void
    {
        // Marshall it to a \DOMElement
        $dhKeyValueElement = self::$dhKeyValue->toXML();

        $xpCache = XPath::getXPath($dhKeyValueElement);

        // Test for an P
        /** @var \DOMElement[] $pElements */
        $pElements = XPath::xpQuery($dhKeyValueElement, './xenc:P', $xpCache);
        $this->assertCount(1, $pElements);

        // Test ordering of DHKeyValue contents
        /** @var \DOMElement[] $dhKeyValueElements */
        $dhKeyValueElements = XPath::xpQuery(
            $dhKeyValueElement,
            './xenc:P/following-sibling::*',
            $xpCache,
        );

        $this->assertCount(5, $dhKeyValueElements);
        $this->assertEquals('xenc:Q', $dhKeyValueElements[0]->tagName);
        $this->assertEquals('xenc:Generator', $dhKeyValueElements[1]->tagName);
        $this->assertEquals('xenc:Public', $dhKeyValueElements[2]->tagName);
        $this->assertEquals('xenc:seed', $dhKeyValueElements[3]->tagName);
        $this->assertEquals('xenc:pgenCounter', $dhKeyValueElements[4]->tagName);
    }
}
