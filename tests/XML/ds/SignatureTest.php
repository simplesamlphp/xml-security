<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, DsObject, KeyInfo};
use SimpleSAML\XMLSecurity\XML\ds\{Signature, SignatureValue, SignedInfo};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(Signature::class)]
final class SignatureTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     * Set up the test.
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Signature::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Signature.xml',
        );
    }


    /**
     * Test creating a SignatureValue from scratch.
     */
    public function testMarshalling(): void
    {
        $signature = new Signature(
            SignedInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_SignedInfo.xml',
                )->documentElement,
            ),
            SignatureValue::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_SignatureValue.xml',
                )->documentElement,
            ),
            KeyInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_KeyInfo.xml',
                )->documentElement,
            ),
            [
                new DsObject(
                    null,
                    null,
                    null,
                    [
                        new Chunk(
                            DOMDocumentFactory::fromString(
                                '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
                            )->documentElement,
                        ),
                    ],
                ),
            ],
            IDValue::fromString('def456'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signature),
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $signature = new Signature(
            SignedInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_SignedInfo.xml',
                )->documentElement,
            ),
            SignatureValue::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_SignatureValue.xml',
                )->documentElement,
            ),
            KeyInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(__FILE__, 3) . '/resources/xml/ds_KeyInfo.xml',
                )->documentElement,
            ),
            [
                new DsObject(
                    null,
                    null,
                    null,
                    [
                        new Chunk(
                            DOMDocumentFactory::fromString(
                                '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
                            )->documentElement,
                        ),
                    ],
                ),
            ],
            IDValue::fromString('def456'),
        );

        $signatureElement = $signature->toXML();
        $xpCache = XPath::getXPath($signatureElement);

        $signedInfo = XPath::xpQuery($signatureElement, './ds:SignedInfo', $xpCache);
        $this->assertCount(1, $signedInfo);

        /** @var \DOMElement[] $signatureElements */
        $signatureElements = XPath::xpQuery($signatureElement, './ds:SignedInfo/following-sibling::*', $xpCache);

        // Test ordering of Signature contents
        $this->assertCount(3, $signatureElements);
        $this->assertEquals('ds:SignatureValue', $signatureElements[0]->tagName);
        $this->assertEquals('ds:KeyInfo', $signatureElements[1]->tagName);
        $this->assertEquals('ds:Object', $signatureElements[2]->tagName);
    }
}
