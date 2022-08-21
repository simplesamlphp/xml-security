<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\SignatureProperty;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignaturePropertyTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\SignatureProperty
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/saml2
 */
final class SignaturePropertyTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = SignatureProperty::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignatureProperty.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<ssp:HSMSerialNumber xmlns:ssp="urn:x-simplesamlphp:namespace">1234567890</ssp:HSMSerialNumber>'
        );

        $signatureProperty = new SignatureProperty([new Chunk($document->documentElement)], 'https://simplesamlphp.org/some/target', 'abc123');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signatureProperty)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $signatureProperty = SignatureProperty::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('https://simplesamlphp.org/some/target', $signatureProperty->getTarget());
        $this->assertEquals('abc123', $signatureProperty->getId());

        $children = $signatureProperty->getElements();
        $this->assertCount(1, $children);

        $child = $children[0];
        $this->assertEquals('HSMSerialNumber', $child->getLocalName());
        $this->assertEquals('ssp', $child->getPrefix());
        $this->assertEquals('urn:x-simplesamlphp:namespace', $child->getNamespaceURI());
        $this->assertEquals('1234567890', $child->getXML()->textContent);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signatureProperty),
        );
    }
}
