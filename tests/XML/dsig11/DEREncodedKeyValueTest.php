<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue};
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, DEREncodedKeyValue};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\DEREncodedKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(DEREncodedKeyValue::class)]
final class DEREncodedKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DEREncodedKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_DEREncodedKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $derEncodedKeyValue = new DEREncodedKeyValue(
            Base64BinaryValue::fromString(
                'MGYwHwYIKoUDBwEBAQEwEwYHKoUDAgIkAAYIKoUDBwEBAgIDQwAEQLrf0MNTFKvS'
                . 'j6pHRwtsQBdyu07oB36PZ+duQ9rOZhWXQ+acH/dP4uLxdJhZq/Z30cDGD+KND4NZ'
                . 'jp+UZWlzWK0=',
            ),
            IDValue::fromString('phpunit'),
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($derEncodedKeyValue),
        );
    }
}
