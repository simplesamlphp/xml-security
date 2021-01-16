<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSecurity\Test\XML\CustomSigned;

/**
 * Class \SimpleSAML\XMLSecurity\XML\CustomSignedTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\AbstractSignedXMLElement
 * @covers \SimpleSAML\XMLSecurity\XML\SignedElementTrait
 * @covers \SimpleSAML\XMLSecurity\Test\XML\CustomSigned
 *
 * @package simplesamlphp/xml-security
 */
final class SignedElementTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;


    /**
     */
    public function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSigned.xml'
        );
    }


    /**
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<some>Chunk</some>'
        );

        $customSignable = new CustomSignable(new Chunk($document->documentElement));
        $this->assertFalse($customSignable->isEmptyElement());
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval($customSignable)
        );
    }
     */


    /**
    public function testUnmarshalling(): void
    {
        $customSignable = CustomSignable::fromXML($this->document->documentElement);

        $customSignableElement = $customSignable->getElement();
        $customSignableElement = $customSignableElement->getXML();

        $this->assertEquals('some', $customSignableElement->tagName);
        $this->assertEquals(
            'Chunk',
            $customSignableElement->textContent
        );
   }
     */


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(CustomSigned::fromXML($this->document->documentElement))))
        );
    }
}

