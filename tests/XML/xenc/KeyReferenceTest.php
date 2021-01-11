<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\xenc\KeyReference;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc\KeyReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\KeyReference
 *
 * @package simplesamlphp/xml-security
 */
final class KeyReferenceTest extends TestCase
{
    /** @var \DOMDocument $document */
    private DOMDocument $document;

    /** @var \SimpleSAML\XML\Chunk $document */
    private Chunk $reference;


    /**
     */
    public function setup(): void
    {
        $dsNamespace = XMLSecurityDSig::XMLDSIGNS;

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_KeyReference.xml'
        );

        $this->reference = new Chunk(DOMDocumentFactory::fromString(<<<XML
  <ds:Transforms xmlns:ds="{$dsNamespace}">
    <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
      <ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">
        self::xenc:EncryptedKey[@Id="example1"]
      </ds:XPath>
    </ds:Transform>
  </ds:Transforms>
XML
        )->documentElement);
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $keyReference = new KeyReference('#Encrypted_KEY_ID', [$this->reference]);

        $this->assertEquals('#Encrypted_KEY_ID', $keyReference->getURI());

        $references = $keyReference->getReferences();
        $this->assertCount(1, $references);
        $this->assertEquals($this->reference, $references[0]);

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($keyReference)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyReference = KeyReference::fromXML($this->document->documentElement);

        $this->assertEquals('#Encrypted_KEY_ID', $keyReference->getURI());

        $references = $keyReference->getReferences();
        $this->assertCount(1, $references);
        $this->assertEquals($this->reference, $references[0]);

        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($keyReference)
        );
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(KeyReference::fromXML($this->document->documentElement))))
        );
    }
}
