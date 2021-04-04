<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\KeyReference;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc\ReferenceListTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList
 *
 * @package simplesamlphp/xml-security
 */
final class ReferenceListTest extends TestCase
{
    use SerializableXMLTestTrait;

    /** @var \SimpleSAML\XML\Chunk $dataReference */
    private Chunk $dataReference;

    /** @var \SimpleSAML\XML\Chunk $keyReference */
    private Chunk $keyReference;


    /**
     */
    public function setup(): void
    {
        $this->testedClass = ReferenceList::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_ReferenceList.xml'
        );

        $dsNamespace = XMLSecurityDSig::XMLDSIGNS;

        $this->dataReference = new Chunk(DOMDocumentFactory::fromString(<<<XML
    <ds:Transforms xmlns:ds="{$dsNamespace}">
      <ds:Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116">
        <ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">
          self::xenc:EncryptedData[@Id="example1"]
        </ds:XPath>
      </ds:Transform>
    </ds:Transforms>
XML
        )->documentElement);

        $this->keyReference = new Chunk(DOMDocumentFactory::fromString(<<<XML
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
        $referenceList = new ReferenceList(
            [new DataReference('#Encrypted_DATA_ID', [$this->dataReference])],
            [new KeyReference('#Encrypted_KEY_ID', [$this->keyReference])]
        );

        $dataReferences = $referenceList->getDataReferences();
        $this->assertCount(1, $dataReferences);

        $keyReferences = $referenceList->getKeyReferences();
        $this->assertCount(1, $keyReferences);

        $this->assertEquals([$this->dataReference], $dataReferences[0]->getElements());
        $this->assertEquals([$this->keyReference], $keyReferences[0]->getElements());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($referenceList)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $referenceList = ReferenceList::fromXML($this->xmlRepresentation->documentElement);

        $dataReferences = $referenceList->getDataReferences();
        $this->assertCount(1, $dataReferences);

        $keyReferences = $referenceList->getKeyReferences();
        $this->assertCount(1, $keyReferences);

        $this->assertEquals([$this->dataReference], $dataReferences[0]->getElements());
        $this->assertEquals([$this->keyReference], $keyReferences[0]->getElements());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($referenceList)
        );
    }
}
