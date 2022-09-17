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
//        $this->publicKey = PublicKey::fromFile('tests/pubkey.pem');
//        $this->privateKey = PrivateKey::fromFile('tests/privkey.pem');
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
            '9b4ce267d4532a21979ee2c1cc00ab4de0e0161493429fcb868374a6db5a06c6d6dda315125d17e0f89c496f76010be1f065233' .
            '12711cd1ea9f06eaa010722ea7b729a2371c83fefdf40c78b510701084d5bf507e6f7d2cd64ff633dd9c46236f4dbfe0730c3e5' .
            'c2e7464457f3bd1203a5cbe626da6a94773761d23d9b0d964e',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA224
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA224, $this->privateKey);
        $this->assertEquals(
            '12a53e78208ed48f80ef25e4462c3bd146ac11c8eedd65442e1c58c71fef87826ebdea0bc004beddd6979537ebd14531cf4bc84' .
            '09a5f5fe6ee70767faa21c6afb88475943c1cbcbf3bb2a52d95de78c0b2d503bdddd84a56a3737dd268b10b696e3182b2429c94' .
            'db685ca1a7bd5777dd8f746bb1027af8bc77c7f7eb309bd3fc',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA256
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA256, $this->privateKey);
        $this->assertEquals(
            'a35c0d5530fbec5c09c53b73b6fe748a02046fab167f1a5363e4eed2429a02091e51c1ba656b42febac83b2ab1f3c029e7420fd' .
            '67ac400d3ca702bf5ff665c15ed870a6839a956bc35f7b58b43255b82b873625106e6397cc633dbfa2f6906d05c532affa3c403' .
            'a8f1176662cc9d574d191ba8b73c799fba2109edc989ff53f0',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA384
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA384, $this->privateKey);
        $this->assertEquals(
            '68d62a600468a2ca0c144744ff8c59069f134fe88a8ee12c13f1030bafa1faaa2cefd11a6936fd6e75e378837b432a0a09e7d27' .
            '48dfef48cc646b8cbbd6467c442cffe8daa319a16ef7f94fda7abade5d147b97edd77d24eef5d73431b03a52f1913c7381e7d92' .
            '4cf180b7fb67048ea18847a6f93e64f54f407726ea345fb9df',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-SHA512
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA512, $this->privateKey);
        $this->assertEquals(
            'a3bc97ac307d238c1545213ae3e1e90c72cb15be636202f9441c742fe5ad1775d0acd5e0ba9ea1a139fb4dbf217e0bb5f2b95ba' .
            '7d07a0a057775439efdec78c723fc7e5eae3977467366c8f3793195cdd982d0ec36a68c2001e8d22d35dcd7ed3845050f03de8f' .
            'bb7193ae0fdfb64a826e58ed9a8d1c91c543bdf110eac1ead6',
            bin2hex($rsa->sign($this->plaintext)),
        );

        // test RSA-RIPEMD160
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_RIPEMD160, $this->privateKey);
        $this->assertEquals(
            'a1ea231e886d4d53e3939f1b8d604db0b74547827a2cd552bbfbd9a07e3f997446606bcb3da052865ed4c7e225ce4c7a02c1141' .
            'ce3ce7079a8f08dd6af07bff9979cb4998e76e36ee5508149c9487b38e7c88056ecad6f16d71eab25173e2d924b7165789d738e' .
            '88cc0371c63da53182365757e29ae48f6330c6fcaa1011812f',
            bin2hex($rsa->sign($this->plaintext)),
        );
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
                '9b4ce267d4532a21979ee2c1cc00ab4de0e0161493429fcb868374a6db5a06c6d6dda315125d17e0f89c496f76010be1f06' .
                '523312711cd1ea9f06eaa010722ea7b729a2371c83fefdf40c78b510701084d5bf507e6f7d2cd64ff633dd9c46236f4dbfe' .
                '0730c3e5c2e7464457f3bd1203a5cbe626da6a94773761d23d9b0d964e',
            ),
        ));

        // test RSA-SHA224
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA224, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '12a53e78208ed48f80ef25e4462c3bd146ac11c8eedd65442e1c58c71fef87826ebdea0bc004beddd6979537ebd14531cf4' .
                'bc8409a5f5fe6ee70767faa21c6afb88475943c1cbcbf3bb2a52d95de78c0b2d503bdddd84a56a3737dd268b10b696e3182' .
                'b2429c94db685ca1a7bd5777dd8f746bb1027af8bc77c7f7eb309bd3fc',
            ),
        ));

        // test RSA-SHA256
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA256, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                'a35c0d5530fbec5c09c53b73b6fe748a02046fab167f1a5363e4eed2429a02091e51c1ba656b42febac83b2ab1f3c029e74' .
                '20fd67ac400d3ca702bf5ff665c15ed870a6839a956bc35f7b58b43255b82b873625106e6397cc633dbfa2f6906d05c532a' .
                'ffa3c403a8f1176662cc9d574d191ba8b73c799fba2109edc989ff53f0',
            ),
        ));

        // test RSA-SHA384
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA384, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                '68d62a600468a2ca0c144744ff8c59069f134fe88a8ee12c13f1030bafa1faaa2cefd11a6936fd6e75e378837b432a0a09e' .
                '7d2748dfef48cc646b8cbbd6467c442cffe8daa319a16ef7f94fda7abade5d147b97edd77d24eef5d73431b03a52f1913c7' .
                '381e7d924cf180b7fb67048ea18847a6f93e64f54f407726ea345fb9df',
            ),
        ));

        // test RSA-SHA512
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_SHA512, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                'a3bc97ac307d238c1545213ae3e1e90c72cb15be636202f9441c742fe5ad1775d0acd5e0ba9ea1a139fb4dbf217e0bb5f2b' .
                '95ba7d07a0a057775439efdec78c723fc7e5eae3977467366c8f3793195cdd982d0ec36a68c2001e8d22d35dcd7ed384505' .
                '0f03de8fbb7193ae0fdfb64a826e58ed9a8d1c91c543bdf110eac1ead6',
            ),
        ));

        // test RSA-RIPEMD160
        $rsa = $this->factory->getAlgorithm(C::SIG_RSA_RIPEMD160, $this->publicKey);
        $this->assertTrue($rsa->verify(
            $this->plaintext,
            hex2bin(
                'a1ea231e886d4d53e3939f1b8d604db0b74547827a2cd552bbfbd9a07e3f997446606bcb3da052865ed4c7e225ce4c7a02c' .
                '1141ce3ce7079a8f08dd6af07bff9979cb4998e76e36ee5508149c9487b38e7c88056ecad6f16d71eab25173e2d924b7165'.
                '789d738e88cc0371c63da53182365757e29ae48f6330c6fcaa1011812f',
            ),
        ));
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
