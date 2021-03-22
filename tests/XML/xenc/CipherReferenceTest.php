<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\xenc\CipherReference;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc\CipherReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CipherReference
 *
 * @package simplesamlphp/xml-security
 */
final class CipherReferenceTest extends TestCase
{
    use SerializableXMLTestTrait;

    /** @var \SimpleSAML\XML\Chunk $reference */
    private Chunk $reference;


    /**
     */
    public function setup(): void
    {
        $this->testedClass = CipherReference::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_CipherReference.xml'
        );

        $dsNamespace = XMLSecurityDSig::XMLDSIGNS;

        $this->reference = new Chunk(DOMDocumentFactory::fromString(<<<XML
 <ds:Transforms xmlns:ds="{$dsNamespace}">
    <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
      <ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">
        self::xenc:CipherValue[@Id="example1"]
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
        $cipherReference = new CipherReference('#Cipher_VALUE_ID', [$this->reference]);

        $this->assertEquals('#Cipher_VALUE_ID', $cipherReference->getURI());

        $references = $cipherReference->getReferences();
        $this->assertCount(1, $references);
        $this->assertEquals($this->reference, $references[0]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherReference)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $cipherReference = CipherReference::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('#Cipher_VALUE_ID', $cipherReference->getURI());

        $references = $cipherReference->getReferences();
        $this->assertCount(1, $references);
        $this->assertEquals($this->reference, $references[0]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherReference)
        );
    }
}
