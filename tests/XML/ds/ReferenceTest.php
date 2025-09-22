<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, Base64BinaryValue, IDValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Type\DigestValue as DigestValueType;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, DigestMethod, DigestValue};
use SimpleSAML\XMLSecurity\XML\ds\{Reference, Transform, Transforms};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ReferenceTest
 *
 * @package simplesamlphp/saml2
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(Reference::class)]
final class ReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Reference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Reference.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $reference = new Reference(
            new DigestMethod(
                AnyURIValue::fromString(C::DIGEST_SHA256),
            ),
            new DigestValue(
                DigestValueType::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new Transforms(
                [
                    new Transform(
                        AnyURIValue::fromString(C::XMLDSIG_ENVELOPED),
                    ),
                    new Transform(
                        AnyURIValue::fromString(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
                    ),
                ],
            ),
            IDValue::fromString('ghi789'),
            AnyURIValue::fromString('urn:some:type'),
            AnyURIValue::fromString('#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'),
        );

        $this->assertFalse($reference->isXPointer());
        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($reference),
        );
    }


    /**
     */
    public function testMarshallingReferenceElementOrdering(): void
    {
        $reference = new Reference(
            new DigestMethod(
                AnyURIValue::fromString(C::DIGEST_SHA256),
            ),
            new DigestValue(
                DigestValueType::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            ),
            new Transforms(
                [
                    new Transform(
                        AnyURIValue::fromString(C::XMLDSIG_ENVELOPED),
                    ),
                    new Transform(
                        AnyURIValue::fromString(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
                    ),
                ],
            ),
            IDValue::fromString('ghi789'),
            AnyURIValue::fromString('urn:some:type'),
            AnyURIValue::fromString('#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'),
        );

        $referenceElement = $reference->toXML();
        /** @var \DOMNodeList<\DOMNode> $children */
        $children = $referenceElement->childNodes;

        $this->assertEquals('ds:Transforms', $children[0]->tagName);
        $this->assertEquals('ds:DigestMethod', $children[1]->tagName);
        $this->assertEquals('ds:DigestValue', $children[2]->tagName);
    }
}
