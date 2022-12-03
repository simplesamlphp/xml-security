<?php

namespace SimpleSAML\XMLSecurity\Test\Alg\Signature;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\RSA;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use TypeError;

use function bin2hex;
use function hex2bin;

/**
 * Tests for SimpleSAML\XMLSecurity\Alg\Signature\RSA.
 *
 * @package SimpleSAML\XMLSecurity\Alg
 */
final class RSASignatureTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected PrivateKey $privateKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected PublicKey $publicKey;

    /** @var string */
    protected string $plaintext = 'plaintext';

    /** @var \SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory */
    protected SignatureAlgorithmFactory $factory;


    public function setUp(): void
    {
        $this->publicKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        $this->privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $this->factory = new SignatureAlgorithmFactory([]);
    }


    /**
     * Test RSA signing.
     */
    public function testSign(): void
    {
        // test RSA-SHA1
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA1, $this->privateKey);
        $this->assertEquals(
            '75780000a403f4280d361c246a1c23e650d59c8cabbe4064e1848bace35ba8931a7c397caacf8af36c10ead3bed5252109a3c12' .
            'a54b3f867950ae75ea29864babef465eabdda826d81c367583725012dfa68a1b51119425e3e6e9490c778db81b5be937ae35f4b' .
            '1393944b7260d4ebd3c100bf59ae42d4506c82cae6550d68b8',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA224
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA224, $this->privateKey);
        $this->assertEquals(
            '9f4bda98aefef8d289595e24663c02108d5273208eb9e530a5a2518082b9e357146517cb630dbaba5f9db7ac297e8b9b1b9248b' .
            'c4b2fbdf009450f4a0759080055b8d3b944d1781fcb03b1fe5039d59a293c54a3e5e8288ecfab9c3a3127f22816753e4aae835d' .
            'e395d52f30768d1d5003e3b124272e89a401909d34c24b9b8f',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA256
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA256, $this->privateKey);
        $this->assertEquals(
            '397f0972d3d52b354298e8f1803efe6b6a77c2213605ab88ff3a38a336e14673c59f69103c1377c5ddf8f9314409ecd865f48b5' .
            '63af2e9ad31121846b0f565fa01898d9ab438ea5278734200400e62cc2bdd31ba86c7b98f8c51dbb22241fbe0535fe2291e9421' .
            '5450ca7fbe4cc8d18420cacce720ac39e09397019c67b8bb2e',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA384
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA384, $this->privateKey);
        $this->assertEquals(
            '6f281ee29c08fa72aaf4f01b095f004608ed82351a160db91933f24118837d43864449862a60eda305d68169a7af12ebe1055b8' .
            '1e5e6a5effca3fd26f0f0db879d83c28a2ef833c021f1b523be2e6947749dbd7daf2d405e341858e95293c724244fc36fccfc05' .
            '27ce1d33e4ff9152e8fbe71eafa0867d076315cb8eb41a5fe5',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA512
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA512, $this->privateKey);
        $this->assertEquals(
            'ce7c0840a26d6bb15e2d386aef8338fff631ca4f1fda22d21b19ee928ed87c7de9ea94f546bcace5dc8cd640a164667686cf836' .
            'bbcbce0f5bbd7c33d9e4262499a8c5c7d8159d090dd4c02fc49eab7e4522ba46258e50ee3278792bba13321780c64980fdd5034' .
            'f79285c07c5f765637950877feb03f94420799bb67b7b9a2ac',
            bin2hex($rsa->sign($this->plaintext)),
        );

        if (boolval(OPENSSL_VERSION_NUMBER >= hexdec('0x30000000')) === false) { // OpenSSL 3.0 disabled RIPEMD160 support
            // test RSA-RIPEMD160
            $rsa = $this->factory->getAlgorithm(C::SIG_RSA_RIPEMD160, $this->privateKey);
            $this->assertEquals(
                '783d2c86bf73b02838be76f832fe1c75e03e9c3ad9055d19737a342c59385744b2332f65f1eac5f5dd0fb88ae7d145ac16d3e83' .
                '916a3be078e426a5ef5ed034a7f69f65ef5e62f6aecac9a896fa9edf473e482fd84ccc93c6677f146bf490af320ae41f4a3fb0f' .
                'a859e68c130a6d321ead6eb0167d1da24a49d97c030d3e8554',
                bin2hex($rsa->sign($this->plaintext)),
            );
        }
    }


    /**
     * Test RSA signature verification.
     */
    public function testVerify(): void
    {
        // test RSA-SHA1
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA1, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '75780000a403f4280d361c246a1c23e650d59c8cabbe4064e1848bace35ba8931a7c397caacf8af36c10ead3bed5252109a' .
                '3c12a54b3f867950ae75ea29864babef465eabdda826d81c367583725012dfa68a1b51119425e3e6e9490c778db81b5be93' .
                '7ae35f4b1393944b7260d4ebd3c100bf59ae42d4506c82cae6550d68b8',
            ),
        ));

        // test RSA-SHA224
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA224, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '9f4bda98aefef8d289595e24663c02108d5273208eb9e530a5a2518082b9e357146517cb630dbaba5f9db7ac297e8b9b1b9248b' .
                'c4b2fbdf009450f4a0759080055b8d3b944d1781fcb03b1fe5039d59a293c54a3e5e8288ecfab9c3a3127f22816753e4aae835d' .
                'e395d52f30768d1d5003e3b124272e89a401909d34c24b9b8f',
            ),
        ));

        // test RSA-SHA256
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA256, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '397f0972d3d52b354298e8f1803efe6b6a77c2213605ab88ff3a38a336e14673c59f69103c1377c5ddf8f9314409ecd865f48b5' .
                '63af2e9ad31121846b0f565fa01898d9ab438ea5278734200400e62cc2bdd31ba86c7b98f8c51dbb22241fbe0535fe2291e9421' .
                '5450ca7fbe4cc8d18420cacce720ac39e09397019c67b8bb2e',
            ),
        ));

        // test RSA-SHA384
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA384, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '6f281ee29c08fa72aaf4f01b095f004608ed82351a160db91933f24118837d43864449862a60eda305d68169a7af12ebe1055b8' .
                '1e5e6a5effca3fd26f0f0db879d83c28a2ef833c021f1b523be2e6947749dbd7daf2d405e341858e95293c724244fc36fccfc05' .
                '27ce1d33e4ff9152e8fbe71eafa0867d076315cb8eb41a5fe5',
            ),
        ));

        // test RSA-SHA512
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA512, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                'ce7c0840a26d6bb15e2d386aef8338fff631ca4f1fda22d21b19ee928ed87c7de9ea94f546bcace5dc8cd640a164667686cf836' .
                'bbcbce0f5bbd7c33d9e4262499a8c5c7d8159d090dd4c02fc49eab7e4522ba46258e50ee3278792bba13321780c64980fdd5034' .
                'f79285c07c5f765637950877feb03f94420799bb67b7b9a2ac',
            ),
        ));

        if (boolval(OPENSSL_VERSION_NUMBER >= hexdec('0x30000000')) === false) { // OpenSSL 3.0 disabled RIPEMD160 support
            // test RSA-RIPEMD160
            $rsa = $this->factory->getAlgorithm(C::SIG_RSA_RIPEMD160, $this->publicKey);
            $this->assertTrue($rsa->verify(
                $this->plaintext,
                hex2bin(
                    '783d2c86bf73b02838be76f832fe1c75e03e9c3ad9055d19737a342c59385744b2332f65f1eac5f5dd0fb88ae7d145ac16d3e83' .
                    '916a3be078e426a5ef5ed034a7f69f65ef5e62f6aecac9a896fa9edf473e482fd84ccc93c6677f146bf490af320ae41f4a3fb0f' .
                    'a859e68c130a6d321ead6eb0167d1da24a49d97c030d3e8554',
                ),
            ));
        }
    }


    /**
     * Test that verification fails properly.
     */
    public function testVerificationFailure(): void
    {
        // test wrong plaintext
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA1, $this->publicKey);
        $this->assertFalse($rsa->verify(
            $this->plaintext . '.',
            '002e8007f09d327b48a7393c9e3666c0d0d73a437804d6e71191bc227546d62351cda58173d69dd792c783337c4ed903a59' .
            'b6fdfd221a0dd22e8632e66c020e1c07400b02625fcdb3821495593e0e0a776a616a2cdf268b3070f7d02e78fdc531c0275' .
            '9ad1fc292ee2f77dcb8a0232cb32e8808c57cb592329d48168bc73936d468421a83446a429cd03bd82aa4a099c2585e0ee6' .
            '0e8afc9b7731d07b00ac8e9f8e7e8c0f526506520c717af5926395b49e6644015e166b462649f65a7d9728ce8872d3b6b02' .
            '19550b4944cb6286e1278908c516be2391928df8d81298e619d0a8711c58e79e5536d7c39fa1b1ffc81d96be6e1b733a824' .
            '8d5fee2866c7f6e48',
        ));

        // test wrong signature
        $this->assertFalse($rsa->verify(
            $this->plaintext,
            '002e8007f09d327b48a7393c9e3666c0d0d73a437804d6e71191bc227546d62351cda58173d69dd792c783337c4ed903a59' .
            'b6fdfd221a0dd22e8632e66c020e1c07400b02625fcdb3821495593e0e0a776a616a2cdf268b3070f7d02e78fdc531c0275' .
            '9ad1fc292ee2f77dcb8a0232cb32e8808c57cb592329d48168bc73936d468421a83446a429cd03bd82aa4a099c2585e0ee6' .
            '0e8afc9b7731d07b00ac8e9f8e7e8c0f526506520c717af5926395b49e6644015e166b462649f65a7d9728ce8872d3b6b02' .
            '19550b4944cb6286e1278908c516be2391928df8d81298e619d0a8711c58e79e5536d7c39fa1b1ffc81d96be6e1b733a824' .
            '8d5fee2866c7f6e48',
        ));

        // test wrong key
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA1, PEMCertificatesMock::getPublicKey(PEMCertificatesMock::OTHER_PUBLIC_KEY));
        $this->assertFalse($rsa->verify(
            $this->plaintext,
            '002e8007f09d327b48a7393c9e3666c0d0d73a437804d6e71191bc227546d62351cda58173d69dd792c783337c4ed903a59' .
            'b6fdfd221a0dd22e8632e66c020e1c07400b02625fcdb3821495593e0e0a776a616a2cdf268b3070f7d02e78fdc531c0275' .
            '9ad1fc292ee2f77dcb8a0232cb32e8808c57cb592329d48168bc73936d468421a83446a429cd03bd82aa4a099c2585e0ee6' .
            '0e8afc9b7731d07b00ac8e9f8e7e8c0f526506520c717af5926395b49e6644015e166b462649f65a7d9728ce8872d3b6b02' .
            '19550b4944cb6286e1278908c516be2391928df8d81298e619d0a8711c58e79e5536d7c39fa1b1ffc81d96be6e1b733a824' .
            '8d5fee2866c7f6e48',
        ));
    }


    /**
     * Test that verification fails when the wrong type of key is passed.
     */
    public function testVerifyWithSymmetricKey(): void
    {
        $key = SymmetricKey::generate(16);

        $this->expectException(TypeError::class);
        new RSA($key);
    }
}
