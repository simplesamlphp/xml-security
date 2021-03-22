<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XMLSecurityDsig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc\CipherDataTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CipherData
 *
 * @package simplesamlphp/xml-security
 */
final class CipherDataTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    public function setup(): void
    {
        $this->testedClass = CipherData::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_CipherData.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshallingCipherValue(): void
    {
        $cipherData = new CipherData('c29tZSB0ZXh0');

        $this->assertEquals('c29tZSB0ZXh0', $cipherData->getCipherValue());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherData)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $cipherData = CipherData::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('c29tZSB0ZXh0', $cipherData->getCipherValue());
    }
}
