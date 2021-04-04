<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\xenc\CipherReference;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\CipherReferenceTest
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

    /** @var \DOMDocument $transforms */
    private DOMDocument $transforms;


    /**
     */
    public function setup(): void
    {
        $this->testedClass = CipherReference::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_CipherReference.xml'
        );

        $dsNamespace = XMLSecurityDSig::XMLDSIGNS;

        $this->transforms = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Transforms.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $cipherReference = new CipherReference('#Cipher_VALUE_ID', [Transforms::fromXML($this->transforms->documentElement)]);

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

        $references = $cipherReference->getElements();
        $this->assertCount(1, $references);
        $this->assertEquals($this->transforms, $references[0]->toXML());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherReference)
        );
    }
}
