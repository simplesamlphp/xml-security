<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Test\XML\EncryptedCustom;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\EncryptableElementTrait;
use SimpleSAML\XMLSecurity\XML\EncryptedElementTrait;

use function dirname;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\EncryptedCustomTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(EncryptableElementTrait::class)]
#[CoversClass(EncryptedElementTrait::class)]
#[CoversClass(EncryptedCustom::class)]
class EncryptedCustomTest extends TestCase
{
    /** @var \DOMElement */
    private DOMElement $signedDocument;

    /** @var PrivateKey */
    protected PrivateKey $privKey;

    /** @var PublicKey */
    protected PublicKey $pubKey;


    /**
     */
    public function setUp(): void
    {
        $this->signedDocument = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignableSigned.xml',
        )->documentElement;

        $this->privKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $this->pubKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
    }


    /**
     * Test encrypting an object and then decrypting it.
     */
    public function testEncryptAndDecryptSharedSecret(): void
    {
        // instantiate
        $customSigned = CustomSignable::fromXML($this->signedDocument);
        $sharedKey = SymmetricKey::generate(16);

        // encrypt
        $factory = new EncryptionAlgorithmFactory();
        $encryptor = $factory->getAlgorithm(C::BLOCK_ENC_AES128, $sharedKey);
        $encryptedCustom = new EncryptedCustom($customSigned->encrypt($encryptor));

        // decrypt
        $decryptedCustom = $encryptedCustom->decrypt($encryptor);

        $this->assertEquals($customSigned, $decryptedCustom);
    }


    /**
     * Test encrypting an object with a session key and asymmetric encryption, then decrypting it.
     */
    public function testEncryptAndDecryptSessionKey(): void
    {
        // instantiate
        $customSigned = CustomSignable::fromXML($this->signedDocument);

        // encrypt
        $factory = new KeyTransportAlgorithmFactory();
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->pubKey);
        $encryptedCustom = new EncryptedCustom($customSigned->encrypt($encryptor));

        // decrypt
        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $this->privKey);
        $decryptedCustom = $encryptedCustom->decrypt($decryptor);

        $this->assertEquals($customSigned, $decryptedCustom);
    }
}
