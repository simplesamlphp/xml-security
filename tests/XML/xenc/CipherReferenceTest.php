<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\XML\ds\{Transform, XPath};
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractReference, AbstractXencElement, CipherReference, Transforms};
use SimpleSAML\XPath\Constants as XPATH_C;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\CipherReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CipherReference
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractReference::class)]
#[CoversClass(CipherReference::class)]
final class CipherReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\XMLSecurity\XML\xenc\Transforms $transforms */
    private static Transforms $transforms;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = CipherReference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_CipherReference.xml',
        );

        $transform = new Transform(
            AnyURIValue::fromString(XPATH_C::XPATH10_URI),
            new XPath(
                StringValue::fromString('count(//. | //@* | //namespace::*)'),
            ),
        );
        self::$transforms = new Transforms([$transform]);
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $cipherReference = new CipherReference(
            AnyURIValue::fromString('#Cipher_VALUE_ID'),
            [self::$transforms],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($cipherReference),
        );
    }
}
