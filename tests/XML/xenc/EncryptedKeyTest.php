<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\XMLSecurityDsig;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptedKeyTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptedType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey
 *
 * @package simplesamlphp/xml-security
 */
final class EncryptedKeyTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    public function setup(): void
    {
        $this->testedClass = EncryptedKey::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/xenc_EncryptedKey.xml'
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData('PzA5X...'),
            'Encrypted_KEY_ID',
            'http://www.w3.org/2001/04/xmlenc#Element',
            'text/plain',
            'someEncoding',
            'some_ENTITY_ID',
            'Name of the key',
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-1_5'),
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
            ),
            new ReferenceList([new DataReference('#Encrypted_DATA_ID')])
        );

        $cipherData = $encryptedKey->getCipherData();
        $this->assertEquals('PzA5X...', $cipherData->getCipherValue());

        $encryptionMethod = $encryptedKey->getEncryptionMethod();
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#rsa-1_5', $encryptionMethod->getAlgorithm());

        $keyInfo = $encryptedKey->getKeyInfo();
        $info = $keyInfo->getInfo();
        $this->assertCount(1, $info);

        $encKey = $info[0];
        $this->assertInstanceOf(EncryptedKey::class, $encKey);

        $referenceList = $encryptedKey->getReferenceList();
        $this->assertEmpty($referenceList->getKeyReferences());
        $dataRefs = $referenceList->getDataReferences();
        $this->assertCount(1, $dataRefs);
        $this->assertEquals('#Encrypted_DATA_ID', $dataRefs[0]->getURI());

        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#Element', $encryptedKey->getType());
        $this->assertEquals('someEncoding', $encryptedKey->getEncoding());
        $this->assertEquals('text/plain', $encryptedKey->getMimeType());
        $this->assertEquals('Encrypted_KEY_ID', $encryptedKey->getID());
        $this->assertEquals('some_ENTITY_ID', $encryptedKey->getRecipient());
        $this->assertEquals('Name of the key', $encryptedKey->getCarriedKeyName());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($encryptedKey)
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData('PzA5X...'),
            'Encrypted_KEY_ID',
            'http://www.w3.org/2001/04/xmlenc#Element',
            'text/plain',
            'someEncoding',
            'some_ENTITY_ID',
            'Name of the key',
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-1_5'),
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
            ),
            new ReferenceList([new DataReference('#Encrypted_DATA_ID')])
        );

        // Marshall it to a \DOMElement
        $encryptedKeyElement = $encryptedKey->toXML();

        // Test for a ReferenceList
        $encryptedKeyElements = XMLUtils::xpQuery($encryptedKeyElement, './xenc:ReferenceList');
        $this->assertCount(1, $encryptedKeyElements);

        // Test ordering of EncryptedKey contents
        $encryptedKeyElements = XMLUtils::xpQuery(
            $encryptedKeyElement,
            './xenc:ReferenceList/following-sibling::*'
        );
        $this->assertCount(1, $encryptedKeyElements);
        $this->assertEquals('xenc:CarriedKeyName', $encryptedKeyElements[0]->tagName);
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $encryptedKey = EncryptedKey::fromXML($this->xmlRepresentation->documentElement);

        $cipherData = $encryptedKey->getCipherData();
        $this->assertEquals('PzA5X...', $cipherData->getCipherValue());

        $encryptionMethod = $encryptedKey->getEncryptionMethod();
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#rsa-1_5', $encryptionMethod->getAlgorithm());

        $keyInfo = $encryptedKey->getKeyInfo();
        $info = $keyInfo->getInfo();
        $this->assertCount(1, $info);

        $encKey = $info[0];
        $this->assertInstanceOf(EncryptedKey::class, $encKey);

        $referenceList = $encryptedKey->getReferenceList();
        $this->assertEmpty($referenceList->getKeyReferences());
        $dataRefs = $referenceList->getDataReferences();
        $this->assertCount(1, $dataRefs);
        $this->assertEquals('#Encrypted_DATA_ID', $dataRefs[0]->getURI());

        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#Element', $encryptedKey->getType());
        $this->assertEquals('someEncoding', $encryptedKey->getEncoding());
        $this->assertEquals('text/plain', $encryptedKey->getMimeType());
        $this->assertEquals('Encrypted_KEY_ID', $encryptedKey->getID());
        $this->assertEquals('some_ENTITY_ID', $encryptedKey->getRecipient());
        $this->assertEquals('Name of the key', $encryptedKey->getCarriedKeyName());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($encryptedKey)
        );
    }
}
