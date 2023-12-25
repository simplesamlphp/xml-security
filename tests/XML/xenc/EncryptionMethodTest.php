<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\MissingAttributeException;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XML\xenc\KeySize;
use SimpleSAML\XMLSecurity\XML\xenc\OAEPparams;
use SimpleSAML\XMLSecurity\Constants as C;

use function dirname;
use function strval;

/**
 * Tests for the xenc:EncryptionMethod element.
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptionMethod
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod
 * @package simplesamlphp/xml-security
 */
final class EncryptionMethodTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptionMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptionMethod.xml',
        );
    }


    // test marshalling


    /**
     * Test creating an EncryptionMethod object from scratch.
     */
    public function testMarshalling(): void
    {
        $alg = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
        $chunkXml = DOMDocumentFactory::fromString('<other:Element xmlns:other="urn:other:enc">Value</other:Element>');
        $chunk = Chunk::fromXML($chunkXml->documentElement);

        $em = new EncryptionMethod($alg, new KeySize(10), new OAEPparams('9lWu3Q=='), [$chunk]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($em),
        );
    }


    /**
     * Test that creating an EncryptionMethod object from scratch works when no optional elements have been specified.
     */
    public function testMarshallingWithoutOptionalParameters(): void
    {
        $em = new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p');
        $document = DOMDocumentFactory::fromString(
            '<xenc:EncryptionMethod xmlns:xenc="' . C::NS_XENC .
            '" Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"/>',
        );

        $this->assertNull($em->getKeySize());
        $this->assertNull($em->getOAEPParams());
        $this->assertEmpty($em->getElements());
        $this->assertEquals(
            $document->saveXML($document->documentElement),
            strval($em),
        );
    }


    public function testMarshallingElementOrdering(): void
    {
        $alg = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
        $chunkXml = DOMDocumentFactory::fromString('<other:Element xmlns:other="urn:other:enc">Value</other:Element>');
        $chunk = Chunk::fromXML($chunkXml->documentElement);

        $em = new EncryptionMethod($alg, new KeySize(10), new OAEPparams('9lWu3Q=='), [$chunk]);

        // Marshall it to a \DOMElement
        $emElement = $em->toXML();

        $xpCache = XPath::getXPath($emElement);

        // Test for a KeySize
        /** @var \DOMElement[] $keySizeElements */
        $keySizeElements = XPath::xpQuery($emElement, './xenc:KeySize', $xpCache);
        $this->assertCount(1, $keySizeElements);
        $this->assertEquals('10', $keySizeElements[0]->textContent);

        // Test ordering of EncryptionMethod contents
        /** @var \DOMElement[] $emElements */
        $emElements = XPath::xpQuery($emElement, './xenc:KeySize/following-sibling::*', $xpCache);

        $this->assertCount(2, $emElements);
        $this->assertEquals('xenc:OAEPparams', $emElements[0]->tagName);
        $this->assertEquals('other:Element', $emElements[1]->tagName);
    }


    // test unmarshalling


    /**
     * Test that creating an EncryptionMethod object from XML without an Algorithm attribute fails.
     */
    public function testUnmarshallingWithoutAlgorithm(): void
    {
        $xmlRepresentation = clone self::$xmlRepresentation->documentElement;
        $xmlRepresentation->removeAttribute('Algorithm');

        $this->expectException(MissingAttributeException::class);
        $this->expectExceptionMessage('Missing \'Algorithm\' attribute on xenc:EncryptionMethod.');
        EncryptionMethod::fromXML($xmlRepresentation);
    }


    /**
     * Test that creating an EncryptionMethod object from XML works if no optional elements are present.
     */
    public function testUnmarshallingWithoutOptionalParameters(): void
    {
        $xencns = C::NS_XENC;
        $document = DOMDocumentFactory::fromString(<<<XML
<xenc:EncryptionMethod xmlns:xenc="{$xencns}" Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p"/>
XML
        );

        $em = EncryptionMethod::fromXML($document->documentElement);
        $this->assertNull($em->getKeySize());
        $this->assertNull($em->getOAEPParams());
        $this->assertEmpty($em->getElements());
        $this->assertEquals(
            $document->saveXML($document->documentElement),
            strval($em),
        );
    }
}
