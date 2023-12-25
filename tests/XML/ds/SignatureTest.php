<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\DsObject;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\ds\SignatureValue;
use SimpleSAML\XMLSecurity\XML\ds\SignedInfo;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Signature
 *
 * @package simplesamlphp/xml-security
 */
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

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

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
            'def456',
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
            'def456',
        );

        $signatureElement = $signature->toXML();
        $xpCache = XPath::getXPath($signatureElement);

        $signedInfo = XPath::xpQuery($signatureElement, './ds:SignedInfo', $xpCache);
        $this->assertCount(1, $signedInfo);

        /** @psalm-var \DOMElement[] $signatureElements */
        $signatureElements = XPath::xpQuery($signatureElement, './ds:SignedInfo/following-sibling::*', $xpCache);

        // Test ordering of Signature contents
        $this->assertCount(3, $signatureElements);
        $this->assertEquals('ds:SignatureValue', $signatureElements[0]->tagName);
        $this->assertEquals('ds:KeyInfo', $signatureElements[1]->tagName);
        $this->assertEquals('ds:Object', $signatureElements[2]->tagName);
    }
}
