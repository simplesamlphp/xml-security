<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend;

use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

use function bin2hex;
use function dirname;
use function hex2bin;

/**
 * Tests for SimpleSAML\XMLSecurity\Backend\OpenSSL.
 *
 * @package SimpleSAML\XMLSecurity\Backend
 */
final class OpenSSLTest extends TestCase
{
    /** @var string */
    protected const VALIDSIG =
        'cdd80e925e509f954807448217157367c00f7ff53c5eec74ea51ef5fee48a048283b37639c7f43400631fa2b9063a1ed057' .
        '104721887a10ad62f128c26e01f363538a84ad261f40b80df86de9cc920d1dce2c27058da81d9c7aa0e68e459ab94995e27' .
        'e57d183ff08188b338f7975681ad67b1b6f8d174b57b666f787b801df9511d7a90e90e9af2386f4051669a4763ce5e9720f' .
        'c8ae2bc90e7c33d92a4bcecefddb06599b1f3adf48cde42d442d76c4d938d1570379bf1ab45feae95f94f48a460a8894f90' .
        'e0208ba93d86b505f32942f53bdab8e506ba227cc813cd26a0ba9a93c46f27dd0c2b7452fd8c79c7aa72b885d95ef6d1dc8' .
        '10829b0832abe290d';

    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected static PrivateKey $privKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $pubKey;

    /** @var \SimpleSAML\XMLSecurity\Backend\OpenSSL */
    protected static OpenSSL $backend;

    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $sharedKey;


    public static function setUpBeforeClass(): void
    {
        self::$privKey = PrivateKey::fromFile(
            'file://' . dirname(__FILE__, 3) . '/resources/keys/privkey.pem',
        );
        self::$pubKey = PublicKey::fromFile(
            'file://' . dirname(__FILE__, 3) . '/resources/keys/pubkey.pem',
        );
        self::$sharedKey = new SymmetricKey(hex2bin('54c98b0ea7d98186c27a6c0c6f35ee1a'));
        self::$backend = new OpenSSL();
        self::$backend->setDigestAlg(C::DIGEST_SHA256);
        self::$backend->setCipher(C::BLOCK_ENC_AES256_GCM);
    }


    /**
     * Test that signing works.
     */
    public function testSign(): void
    {
        $this->assertEquals(self::VALIDSIG, bin2hex(self::$backend->sign(self::$privKey, 'Signed text')));
    }


    /**
     * Test signing with something that's not a private key.
     */
    public function testSignFailure(): void
    {
        $k = SymmetricKey::generate(10);
        $this->expectException(RuntimeException::class);
        @self::$backend->sign($k, 'Signed text');
    }


    /**
     * Test the verification of signatures.
     */
    public function testVerify(): void
    {
        // test successful verification
        $this->assertTrue(self::$backend->verify(self::$pubKey, 'Signed text', hex2bin(self::VALIDSIG)));

        // test forged signature
        $wrongSig = self::VALIDSIG;
        $wrongSig[10] = '6';
        $this->assertFalse(self::$backend->verify(self::$pubKey, 'Signed text', hex2bin($wrongSig)));
    }


    /**
     * Test encryption.
     */
    public function testEncrypt(): void
    {
        // test symmetric encryption
        self::$backend->setCipher(C::BLOCK_ENC_AES128);
        $this->assertNotEmpty(self::$backend->encrypt(self::$sharedKey, 'Plaintext'));
        self::$backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);

        // test encryption with public key
        $this->assertNotEmpty(self::$backend->encrypt(self::$pubKey, 'Plaintext'));

        // test encryption with private key
        $this->assertNotEmpty(self::$backend->encrypt(self::$privKey, 'Plaintext'));
    }


    /**
     * Test decryption.
     */
    public function testDecrypt(): void
    {
        // test decryption with symmetric key
        self::$backend->setCipher(C::BLOCK_ENC_AES128);
        $this->assertEquals(
            'Plaintext',
            self::$backend->decrypt(
                self::$sharedKey,
                hex2bin('9faa2195bd89d2b8b3721f4fea39e904250096ad2bcd66cf77f8423af83d18ba'),
            ),
        );

        // test decryption with private key
        self::$backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);
        $this->assertEquals(
            'Plaintext',
            self::$backend->decrypt(
                self::$privKey,
                hex2bin(
                    'c2aa74a85de59daef76c4f4736680ff55503d1ce991a6b947ad5d269b93ef97acf761c1c1ccfedc1382d2c16ea52b7f' .
                    '6b298d8a0f6dbf5e46c41df70804888758e2b95502d9b0849c8d670e4bb9f13bb9afa1d51a76a32625513599c4a2d84' .
                    '1cb79beec171b9c0cf11466e90187e91377a7f7582f3eec3df6703a1abda89339d0f490bca61ceac743be401d861d50' .
                    'eb6aaa2db63264cd2013e4008d82c4e7b3f8f13447cf136e52c9b9f06c062a3fe66d3b9f7fa78281d149e7756a97edb' .
                    '0b2a500f110587f2d81790922def9061c4d8d500cd67ade406b61a20a8fe3b7db1ccc69095a20f556e5ed1f91ccaff1' .
                    'cb3f13065ebee9e20064b0a75edb2b603af6c',
                ),
            ),
        );

        // test decryption with public key
        $this->assertEquals(
            'Plaintext',
            self::$backend->decrypt(
                self::$pubKey,
                hex2bin(
                    'd012f638b7814f63cce16d1938d34e1f82abcbe925cf579a4dd6e5b0d8f0c524b77a94423625c1cec7cc45e26f37188' .
                    'ff18870cd4f8cd3e0de6084413c71c1f4f14f04858a655162e9332f4b26fe4523cebf7de51267290f8ae290c869fb32' .
                    '4570d9065b9604587111b116e8d15d8ef820f2ea2c1ae129ce27a20c4a7e4df815fb47a047cd11b06ada9f4ad881545' .
                    '2380a09fb6bff787ff167a20662740e1ac034e66612e2194d8b60a22341032d758fd94221314125dbb2d1432b4a3633' .
                    'b0857d8d4938aabe1b53ab5f970fb4ad0ed0a554771cfa819cffba8ec5935a6d2f706dfcada355da34b994691c76a60' .
                    'd10c746a5b683b2a0080d847ff208cf240a1c',
                ),
            ),
        );
    }


    /**
     * Test that RSA-OAEP and RSA-OAEP-MGF1P are equivalent by default.
     */
    public function testEquivalentOAEP(): void
    {
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $ciphertext = self::$backend->encrypt(self::$pubKey, 'Plaintext');
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$privKey, $ciphertext));
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$privKey, $ciphertext));
    }


    /**
     * Test that encrypting with RSA 1.5 and decrypting with RSA-OAEP* fails.
     */
    public function testEncryptRSA15DecryptOAEP(): void
    {
        self::$backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);
        $ciphertext = self::$backend->encrypt(self::$pubKey, 'Plaintext');
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Cannot decrypt data;/');
        self::$backend->decrypt(self::$privKey, $ciphertext);
    }


    /**
     * Test that encrypting with RSA-OAEP* and decrypting with RSA 1.5 fails.
     */
    #[RequiresOperatingSystem('Linux')]
    public function testEncryptOAEPDecryptRSA15Unix(): void
    {
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $ciphertext = self::$backend->encrypt(self::$pubKey, 'Plaintext');
        self::$backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);
        $plaintext = self::$backend->decrypt(self::$privKey, $ciphertext);
        $this->assertNotEquals('Plaintext', $plaintext);
    }


    /**
     * Test that encrypting with RSA-OAEP* and decrypting with RSA 1.5 fails.
     */
    #[RequiresOperatingSystem('Windows')]
    public function testEncryptOAEPDecryptRSA15Windows(): void
    {
        self::$backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $ciphertext = self::$backend->encrypt(self::$pubKey, 'Plaintext');
        self::$backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Cannot decrypt data:/');
        self::$backend->decrypt(self::$privKey, $ciphertext);
    }


    /**
     * Test that CBC and GCM modes are incompatible.
     */
    public function testMismatchingSymmetricEncryptionAlgorithm(): void
    {
        self::$backend->setCipher(C::BLOCK_ENC_AES128);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        self::$backend->setCipher(C::BLOCK_ENC_AES128_GCM);
        $this->expectException(RuntimeException::class);
        $plaintext = self::$backend->decrypt(self::$sharedKey, $ciphertext);
    }


    /**
     * Test that all symmetric encryption CBC modes work.
     */
    public function testSymmetricCBCEncryption(): void
    {
        self::$backend->setCipher(C::BLOCK_ENC_3DES);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));

        self::$backend->setCipher(C::BLOCK_ENC_AES128);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));

        self::$backend->setCipher(C::BLOCK_ENC_AES192);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));

        self::$backend->setCipher(C::BLOCK_ENC_AES256);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));
    }


    /**
     * Test that all symmetric encryption GCM modes work.
     */
    public function testSymmetricGCMEncryption(): void
    {
        self::$backend->setCipher(C::BLOCK_ENC_AES128_GCM);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));

        self::$backend->setCipher(C::BLOCK_ENC_AES192_GCM);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));

        self::$backend->setCipher(C::BLOCK_ENC_AES256_GCM);
        $ciphertext = self::$backend->encrypt(self::$sharedKey, 'Plaintext');
        $this->assertEquals('Plaintext', self::$backend->decrypt(self::$sharedKey, $ciphertext));
    }


    /**
     * Test for wrong digests.
     */
    public function testSetUnknownDigest(): void
    {
        $backend = new OpenSSL();
        $this->expectException(InvalidArgumentException::class);
        $backend->setDigestAlg('foo');
    }



    /**
     * Test ISO 10126 padding.
     */
    public function testPad(): void
    {
        $this->assertEquals('666f6f0d0d0d0d0d0d0d0d0d0d0d0d0d', bin2hex(self::$backend->pad('foo')));
        $this->assertEquals(
            '666f6f626172666f6f626172666f6f6261720e0e0e0e0e0e0e0e0e0e0e0e0e0e',
            bin2hex(self::$backend->pad('foobarfoobarfoobar')),
        );
    }


    /**
     * Test ISO 10126 unpadding.
     */
    public function testUnpad(): void
    {
        $this->assertEquals('foo', self::$backend->unpad(hex2bin('666f6f0d0d0d0d0d0d0d0d0d0d0d0d0d')));
        $this->assertEquals(
            'foobarfoobarfoobar',
            self::$backend->unpad(hex2bin('666f6f626172666f6f626172666f6f6261720e0e0e0e0e0e0e0e0e0e0e0e0e0e')),
        );
    }


    /**
     * Test for wrong ciphers.
     */
    public function testSetUnknownCipher(): void
    {
        $backend = new OpenSSL();
        $this->expectException(InvalidArgumentException::class);
        $backend->setCipher('foo');
    }
}
