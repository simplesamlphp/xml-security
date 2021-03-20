<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use SimpleSAML\Test\XML\SerializableXMLTest;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\MissingAttributeException;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\Constants;

/**
 * Tests for the xenc:EncryptionMethod element.
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptionMethod
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod
 * @package simplesamlphp/xml-security
 */
final class EncryptionMethodTest extends SerializableXMLTest
{
    /**
     */
    protected function setUp(): void
    {
        self::$element = EncryptionMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_EncryptionMethod.xml'
        );
    }


    // test marshalling


    /**
     * Test creating an EncryptionMethod object from scratch.
     */
    public function testMarshalling(): void
    {
        $alg = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
        $chunkXml = DOMDocumentFactory::fromString('<other:Element xmlns:other="urn:other">Value</other:Element>');
        $chunk = Chunk::fromXML($chunkXml->documentElement);

        $em = new EncryptionMethod($alg, 10, '9lWu3Q==', [$chunk]);
        $this->assertEquals($alg, $em->getAlgorithm());
        $this->assertEquals(10, $em->getKeySize());
        $this->assertEquals('9lWu3Q==', $em->getOAEPParams());
        $this->assertCount(1, $em->getChildren());
        $this->assertInstanceOf(Chunk::class, $em->getChildren()[0]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($em)
        );
    }


    /**
     * Test that creating an EncryptionMethod object from scratch works when no optional elements have been specified.
     */
    public function testMarshallingWithoutOptionalParameters(): void
    {
        $em = new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p');
        $document = DOMDocumentFactory::fromString(
            '<xenc:EncryptionMethod xmlns:xenc="' . Constants::XMLENCNS .
            '" Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"/>'
        );

        $this->assertNull($em->getKeySize());
        $this->assertNull($em->getOAEPParams());
        $this->assertEmpty($em->getChildren());
        $this->assertEquals(
            $document->saveXML($document->documentElement),
            strval($em)
        );
    }


    public function testMarshallingElementOrdering(): void
    {
        $alg = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
        $chunkXml = DOMDocumentFactory::fromString('<other:Element xmlns:other="urn:other">Value</other:Element>');
        $chunk = Chunk::fromXML($chunkXml->documentElement);

        $em = new EncryptionMethod($alg, 10, '9lWu3Q==', [$chunk]);

        // Marshall it to a \DOMElement
        $emElement = $em->toXML();

        // Test for a KeySize
        $keySizeElements = XMLUtils::xpQuery($emElement, './xenc:KeySize');
        $this->assertCount(1, $keySizeElements);
        $this->assertEquals('10', $keySizeElements[0]->textContent);

        // Test ordering of EncryptionMethod contents
        $emElements = XMLUtils::xpQuery($emElement, './xenc:KeySize/following-sibling::*');

        $this->assertCount(2, $emElements);
        $this->assertEquals('xenc:OAEPParams', $emElements[0]->tagName);
        $this->assertEquals('other:Element', $emElements[1]->tagName);
    }


    // test unmarshalling


    /**
     * Test creating an EncryptionMethod object from XML.
     */
    public function testUnmarshalling(): void
    {
        $em = EncryptionMethod::fromXML(self::$xmlRepresentation->documentElement);
        $alg = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';

        $this->assertEquals($alg, $em->getAlgorithm());
        $this->assertEquals(10, $em->getKeySize());
        $this->assertEquals('9lWu3Q==', $em->getOAEPParams());
        $this->assertCount(1, $em->getChildren());
        $this->assertInstanceOf(Chunk::class, $em->getChildren()[0]);
    }


    /**
     * Test that creating an EncryptionMethod object from XML without an Algorithm attribute fails.
     */
    public function testUnmarshallingWithoutAlgorithm(): void
    {
        $this->expectException(MissingAttributeException::class);
        $this->expectExceptionMessage('Missing \'Algorithm\' attribute on xenc:EncryptionMethod.');
        self::$xmlRepresentation->documentElement->removeAttribute('Algorithm');
        EncryptionMethod::fromXML(self::$xmlRepresentation->documentElement);
    }


    /**
     * Test that creating an EncryptionMethod object from XML works if no optional elements are present.
     */
    public function testUnmarshallingWithoutOptionalParameters(): void
    {
        $xencns = Constants::XMLENCNS;
        $document = DOMDocumentFactory::fromString(<<<XML
<xenc:EncryptionMethod xmlns:xenc="{$xencns}" Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"/>
XML
        );

        $em = EncryptionMethod::fromXML($document->documentElement);
        $this->assertNull($em->getKeySize());
        $this->assertNull($em->getOAEPParams());
        $this->assertEmpty($em->getChildren());
        $this->assertEquals(
            $document->saveXML($document->documentElement),
            strval($em)
        );
    }
}
