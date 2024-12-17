<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractDHKeyValueType;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement;
use SimpleSAML\XMLSecurity\XML\xenc\DHKeyValue;
use SimpleSAML\XMLSecurity\XML\xenc\Generator;
use SimpleSAML\XMLSecurity\XML\xenc\P;
use SimpleSAML\XMLSecurity\XML\xenc\PgenCounter;
use SimpleSAML\XMLSecurity\XML\xenc\Q;
use SimpleSAML\XMLSecurity\XML\xenc\Seed;
use SimpleSAML\XMLSecurity\XML\xenc\XencPublic;

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
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractDHKeyValueType::class)]
#[CoversClass(DHKeyValue::class)]
final class DHKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DHKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_DHKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $xencPublic = new XencPublic('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $p = new P('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $q = new Q('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $generator = new Generator('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $seed = new Seed('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $pgenCounter = new PgenCounter('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $dhKeyValue = new DHKeyValue($xencPublic, $p, $q, $generator, $seed, $pgenCounter);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($dhKeyValue),
        );
    }
    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $xencPublic = new XencPublic('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $p = new P('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $q = new Q('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $generator = new Generator('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $seed = new Seed('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
        $pgenCounter = new PgenCounter('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $dhKeyValue = new DHKeyValue($xencPublic, $p, $q, $generator, $seed, $pgenCounter);

        // Marshall it to a \DOMElement
        $dhKeyValueElement = $dhKeyValue->toXML();

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
