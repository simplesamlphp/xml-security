<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use SimpleSAML\Test\XML\SerializableXMLTest;
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
final class CipherDataTest extends SerializableXMLTest
{
    /**
     */
    public function setup(): void
    {
        self::$element = CipherData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
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
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($cipherData)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $cipherData = CipherData::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals('c29tZSB0ZXh0', $cipherData->getCipherValue());
    }
}
