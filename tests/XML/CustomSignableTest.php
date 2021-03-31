<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Test\XML\CustomSignable;

/**
 * Class \SimpleSAML\XMLSecurity\XML\CustomSignableTest
 *
 * @covers \SimpleSAML\XMLSecurity\Test\XML\CustomSignable
 *
 * @package simplesamlphp/xml-security
 */
final class SignableElementTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = CustomSignable::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSignable.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<ssp:Some>Chunk</ssp:Some>'
        );

        $customSignable = new CustomSignable($document->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());
        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($customSignable)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $customSignable = CustomSignable::fromXML($this->xmlRepresentation->documentElement);

        $customSignableElement = $customSignable->getElement();

        $this->assertEqualXmlStructure($this->xmlRepresentation->documentElement,  $customSignableElement);
    }
}

