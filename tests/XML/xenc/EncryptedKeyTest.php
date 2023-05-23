<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\RSA;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\CipherValue;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function bin2hex;
use function dirname;
use function strval;

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
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected static PrivateKey $privKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $pubKey;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptedKey::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptedKey.xml',
        );

        self::$privKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        self::$pubKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData(new CipherValue('3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s'
                . '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUdoipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQ'
                . 'V6dF9YSJqehuRFCqVJprIDZNfrKnm7WfwMiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO'
                . 'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLoCn66P/Zt7I9Q==')),
            'Encrypted_KEY_ID',
            'http://www.w3.org/2001/04/xmlenc#Element',
            'text/plain',
            'urn:x-simplesamlphp:encoding',
            'some_ENTITY_ID',
            new CarriedKeyName('Name of the key'),
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-1_5'),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(new CipherValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=')),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod('http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'),
                    ),
                ],
            ),
            new ReferenceList([new DataReference('#Encrypted_DATA_ID')]),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptedKey),
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData(new CipherValue('3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s'
                . '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUdoipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQ'
                . 'V6dF9YSJqehuRFCqVJprIDZNfrKnm7WfwMiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO'
                . 'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLoCn66P/Zt7I9Q==')),
            'Encrypted_KEY_ID',
            'http://www.w3.org/2001/04/xmlenc#Element',
            'text/plain',
            'urn:x-simplesamlphp:encoding',
            'some_ENTITY_ID',
            new CarriedKeyName('Name of the key'),
            new EncryptionMethod('http://www.w3.org/2001/04/xmlenc#rsa-1_5'),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(new CipherValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=')),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod('http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'),
                    ),
                ],
            ),
            new ReferenceList([new DataReference('#Encrypted_DATA_ID')]),
        );

        // Marshall it to a \DOMElement
        $encryptedKeyElement = $encryptedKey->toXML();

        $xpCache = XPath::getXPath($encryptedKeyElement);
        // Test for a ReferenceList
        $encryptedKeyElements = XPath::xpQuery(
            $encryptedKeyElement,
            './xenc:ReferenceList',
            $xpCache,
        );
        $this->assertCount(1, $encryptedKeyElements);

        // Test ordering of EncryptedKey contents
        /** @var \DOMElement[] $encryptedKeyElements */
        $encryptedKeyElements = XPath::xpQuery(
            $encryptedKeyElement,
            './xenc:ReferenceList/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(1, $encryptedKeyElements);
        $this->assertEquals('xenc:CarriedKeyName', $encryptedKeyElements[0]->tagName);
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $encryptedKey = EncryptedKey::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptedKey),
        );
    }


    /**
     * Test encryption and decryption with PKCS1 RSA 1.5.
     */
    public function testPKCS1Encryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(8);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(C::KEY_TRANSPORT_RSA_1_5),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }


    /**
     * Test encryption and decryption with RSA OAEP
     */
    public function testOAEPEncryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(16);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(C::KEY_TRANSPORT_OAEP),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }


    /**
     * Test encryption and decryption with RSA OAEP-MGF1P
     */
    public function testOAEMGF1PPEncryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(16);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(C::KEY_TRANSPORT_OAEP_MGF1P),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }
}
