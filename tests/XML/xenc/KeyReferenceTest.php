<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\xenc\KeyReference;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\KeyReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\KeyReference
 *
 * @package simplesamlphp/xml-security
 */
final class KeyReferenceTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setup(): void
    {
        $this->testedClass = KeyReference::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_KeyReference.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $keyReference = new KeyReference(
            '#Encrypted_KEY_ID',
            [
                new Transforms([
                    new Transform(
                        'http://www.w3.org/TR/1999/REC-xpath-19991116',
                        [
                            new Chunk(
                                DOMDocumentFactory::fromString(
                                    '<ds:XPath xmlns:xenc="http://www.w3.org/2001/04/xmlenc#">self::xenc:EncryptedKey[@Id="example1"]</ds:XPath>'
                                )->documentElement
                            )
                        ]
                    )
                ])
            ]
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($keyReference)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyReference = KeyReference::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('#Encrypted_KEY_ID', $keyReference->getURI());

        $references = $keyReference->getElements();
        $this->assertCount(1, $references);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($keyReference)
        );
    }
}
