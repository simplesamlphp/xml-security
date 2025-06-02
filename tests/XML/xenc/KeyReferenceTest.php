<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XML\Type\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\{Transform, Transforms, XPath};
use SimpleSAML\XMLSecurity\XML\xenc\{AbstractReference, AbstractXencElement, KeyReference};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\KeyReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\KeyReference
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractReference::class)]
#[CoversClass(KeyReference::class)]
final class KeyReferenceTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = KeyReference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_KeyReference.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $keyReference = new KeyReference(
            AnyURIValue::fromString('#Encrypted_KEY_ID'),
            [
                new Transforms(
                    [
                        new Transform(
                            AnyURIValue::fromString(C::XPATH10_URI),
                            new XPath(
                                StringValue::fromString('self::xenc:EncryptedKey[@Id="example1"]'),
                            ),
                        ),
                    ],
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($keyReference),
        );
    }
}
