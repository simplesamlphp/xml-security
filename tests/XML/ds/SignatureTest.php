<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\Signature;

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
    use SerializableXMLTestTrait;


    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->testedClass = Signature::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Signature.xml'
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
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignedInfo.xml'
                )->documentElement
            ),
            SignatureValue::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignatureValue.xml'
                )->documentElement
            ),
            KeyInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_KeyInfo.xml'
                )->documentElement
            ),
            [new Chunk(DOMDocumentFactory::fromString('<ds:Object><some>Chunk</some></ds:Object>')->documentElement)],
            'def456'
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signature)
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $signature = new Signature(
            SignedInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignedInfo.xml'
                )->documentElement
            ),
            SignatureValue::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignatureValue.xml'
                )->documentElement
            ),
            KeyInfo::fromXML(
                DOMDocumentFactory::fromFile(
                    dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_KeyInfo.xml'
                )->documentElement
            ),
            [new Chunk(DOMDocumentFactory::fromString('<ds:Object><some>Chunk</some></ds:Object>')->documentElement)],
            'def456'
        );

        $signatureElement = $signature->toXML();

        $signedInfo = XMLUtils::xpQuery($signatureElement, './ds:SignedInfo');
        $this->assertCount(1, $signedInfo);

        /** @psalm-var \DOMElement[] $signatureElements */
        $signatureElements = XMLUtils::xpQuery($signatureElement, './ds:SignedInfo/following-sibling::*');

        // Test ordering of Signature contents
        $this->assertCount(3, $signatureElements);
        $this->assertEquals('ds:SignatureValue', $signatureElements[0]->tagName);
        $this->assertEquals('ds:KeyInfo', $signatureElements[1]->tagName);
        $this->assertEquals('ds:Object', $signatureElements[2]->tagName);
    }


    /**
     * Test creating a SignatureValue object from XML.
     */
    public function testUnmarshalling(): void
    {
        $signature = Signature::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('def456', $signature->getId());

        $signedInfo = $signature->getSignedInfo();
        $this->assertInstanceOf(SignedInfo::class, $signedInfo);

        $signatureValue = $signature->getSignatureValue();
        $this->assertInstanceOf(SignatureValue::class, $signatureValue);

        $keyInfo = $signature->getKeyInfo();
        $this->assertInstanceOf(KeyInfo::class, $keyInfo);

        $objects = $signature->getObjects();
        $this->assertCount(1, $objects);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signature)
        );
    }
}
