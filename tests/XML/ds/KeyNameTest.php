<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, KeyName};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\KeyNameTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(KeyName::class)]
final class KeyNameTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = KeyName::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_KeyName.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyName = new KeyName(
            StringValue::fromString('testkey'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($keyName),
        );
    }
}
