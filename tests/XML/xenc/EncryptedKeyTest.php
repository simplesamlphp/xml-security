<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, Base64BinaryValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\{PrivateKey, PublicKey, SymmetricKey};
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\{
    AbstractEncryptedType,
    AbstractXencElement,
    CarriedKeyName,
    CipherData,
    CipherValue,
    DataReference,
    EncryptedKey,
    EncryptionMethod,
    ReferenceList,
};

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
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractEncryptedType::class)]
#[CoversClass(EncryptedKey::class)]
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
            new CipherData(
                new CipherValue(
                    Base64BinaryValue::fromString(
                        '3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s' .
                        '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUd' .
                        'oipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQV6dF9YSJqehuRFCqVJprIDZNfrKnm7Wfw' .
                        'MiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO' .
                        'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLo' .
                        'Cn66P/Zt7I9Q==',
                    ),
                ),
            ),
            IDValue::fromString('Encrypted_KEY_ID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            StringValue::fromString('some_ENTITY_ID'),
            new CarriedKeyName(
                StringValue::fromString('Name of the key'),
            ),
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            new CipherValue(
                                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                            ),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
            new ReferenceList([
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                ),
            ]),
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
            new CipherData(
                new CipherValue(
                    Base64BinaryValue::fromString(
                        '3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s' .
                        '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUd' .
                        'oipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQV6dF9YSJqehuRFCqVJprIDZNfrKnm7Wfw' .
                        'MiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO' .
                        'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLo' .
                        'Cn66P/Zt7I9Q==',
                    ),
                ),
            ),
            IDValue::fromString('Encrypted_KEY_ID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            StringValue::fromString('some_ENTITY_ID'),
            new CarriedKeyName(
                StringValue::fromString('Name of the key'),
            ),
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            new CipherValue(
                                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                            ),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
            new ReferenceList([
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                ),
            ]),
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


    /**
     * Test encryption and decryption with PKCS1 RSA 1.5.
     */
    public function testPKCS1Encryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        /** @var \SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmInterface $encryptor */
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(8);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
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
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
            ),
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
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP_MGF1P),
            ),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }
}
