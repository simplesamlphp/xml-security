<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, Exponent, Modulus, RSAKeyValue};
use SimpleSAML\XPath\XPath;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\RSAKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(RSAKeyValue::class)]
final class RSAKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    private static RSAKeyValue $rsaKeyValue;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = RSAKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_RSAKeyValue.xml',
        );

        self::$rsaKeyValue = new RSAKeyValue(
            new Modulus(
                CryptoBinaryValue::fromString('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
            ),
            new Exponent(
                CryptoBinaryValue::fromString('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo='),
            ),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval(self::$rsaKeyValue),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $RSAKeyValueElement = self::$rsaKeyValue->toXML();
        $xpCache = XPath::getXPath($RSAKeyValueElement);

        $modulus = XPath::xpQuery($RSAKeyValueElement, './ds:Modulus', $xpCache);
        $this->assertCount(1, $modulus);

        /** @var \DOMElement[] $RSAKeyValueElements */
        $RSAKeyValueElements = XPath::xpQuery($RSAKeyValueElement, './ds:Modulus/following-sibling::*', $xpCache);

        // Test ordering of RSAKeyValue contents
        $this->assertCount(1, $RSAKeyValueElements);
        $this->assertEquals('ds:Exponent', $RSAKeyValueElements[0]->tagName);
    }
}
