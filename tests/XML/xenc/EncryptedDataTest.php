<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XMLSecurityDsig;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptedDataTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptedType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData
 *
 * @package simplesamlphp/xml-security
 */
final class EncryptedDataTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    public function setup(): void
    {
        $this->testedClass = EncryptedData::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_EncryptedData.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $encryptedData = new EncryptedData(
            new CipherData('iaDc7...'),
            'MyID',
            'http://www.w3.org/2001/04/xmlenc#Element',
            'text/plain',
            'SomeEncoding',
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#aes128-cbc'),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData('nxf0b...'),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod('http://www.w3.org/2001/04/xmldsig-more#rsa-sha256')
                    )
                ]
            )
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($encryptedData)
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $encryptedData = EncryptedData::fromXML($this->xmlRepresentation->documentElement);

        $cipherData = $encryptedData->getCipherData();
        $this->assertEquals('iaDc7...', $cipherData->getCipherValue());

        $encryptionMethod = $encryptedData->getEncryptionMethod();
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#aes128-cbc', $encryptionMethod->getAlgorithm());

        $keyInfo = $encryptedData->getKeyInfo();
        $info = $keyInfo->getInfo();
        $this->assertCount(1, $info);

        $encKey = $info[0];
        $this->assertInstanceOf(EncryptedKey::class, $encKey);

        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#Element', $encryptedData->getType());
        $this->assertEquals('text/plain', $encryptedData->getMimeType());
        $this->assertEquals('MyID', $encryptedData->getID());
        $this->assertEquals('SomeEncoding', $encryptedData->getEncoding());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($encryptedData)
        );
    }
}
