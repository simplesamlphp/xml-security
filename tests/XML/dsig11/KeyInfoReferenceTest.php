<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{AnyURIValue, IDValue};
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, KeyInfoReference};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\KeyInfoReferenceTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(KeyInfoReference::class)]
final class KeyInfoReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = KeyInfoReference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_KeyInfoReference.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $KeyInfoReference = new KeyInfoReference(
            AnyURIValue::fromString('#_e395489e5f8444f1aabb4b2ca98a23b793d211ddf0'),
            IDValue::fromString('abc123'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($KeyInfoReference),
        );
    }
}
