<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Signature;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\HMAC;
use SimpleSAML\XMLSecurity\Alg\Signature\PrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Backend\SignatureBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureAlgorithmFactoryTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $skey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $pkey;


    public static function setUpBeforeClass(): void
    {
        self::$skey = SymmetricKey::generate(16);
        self::$pkey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
    }


    protected function tearDown(): void
    {
        $ref = new \ReflectionClass(SignatureAlgorithmFactory::class);
        $ref->getProperty('cache')->setValue(null, []);
        $ref->getProperty('initialized')->setValue(null, false);
        SignatureAlgorithmFactory::clearAlgorithmFactories();
    }


    /**
     * Test obtaining the digest algorithm from a signature algorithm.
     */
    public function testGetDigestAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([]);

        foreach (C::$HMAC_DIGESTS as $signature => $digest) {
            $alg = $factory->getAlgorithm($signature, self::$skey);
            $this->assertEquals($digest, $alg->getDigest());
        }

        foreach (C::$RSA_DIGESTS as $signature => $digest) {
            $alg = $factory->getAlgorithm($signature, self::$pkey);
            $this->assertEquals($digest, $alg->getDigest());
        }
    }


    /**
     * Test for unsupported algorithms.
     */
    public function testGetUnknownAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([]);
        $this->expectException(UnsupportedAlgorithmException::class);
        $factory->getAlgorithm('Unsupported algorithm identifier', self::$skey);
    }


    /**
     * Test for blacklisted algorithms.
     */
    public function testBlacklistedAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([C::SIG_RSA_SHA1]);
        $algorithm = $factory->getAlgorithm(C::SIG_HMAC_SHA1, self::$skey);
        $this->assertInstanceOf(HMAC::class, $algorithm);
        $this->assertEquals(C::SIG_HMAC_SHA1, $algorithm->getAlgorithmId());
        $this->assertEquals(self::$skey, $algorithm->getKey());

        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::SIG_RSA_SHA1, self::$pkey);
    }


    public function testRegisterAlgorithmFactoryClosureYieldsWrapperOnGetAlgorithm(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(SignatureBackend::class);

        SignatureAlgorithmFactory::registerAlgorithmFactory(
            C::SIG_RSA_SHA256,
            static function (KeyInterface $key, string $algId) use ($pkaBackend): PrivateKeyAgentRSA {
                return new PrivateKeyAgentRSA($key, $algId, $pkaBackend);
            },
        );

        $factory = new SignatureAlgorithmFactory([]);
        $alg = $factory->getAlgorithm(C::SIG_RSA_SHA256, $cert);

        $this->assertSame(C::SIG_RSA_SHA256, $alg->getAlgorithmId());
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $alg);
    }


    public function testBlacklistBlocksClosureRegisteredAlgorithm(): void
    {
        $pkaBackend = $this->createStub(SignatureBackend::class);

        SignatureAlgorithmFactory::registerAlgorithmFactory(
            C::SIG_RSA_SHA256,
            static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
                new PrivateKeyAgentRSA($key, $algId, $pkaBackend),
        );

        $factory = new SignatureAlgorithmFactory([C::SIG_RSA_SHA256]);
        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$pkey);
    }


    public function testUnregisterAlgorithmFactoryRestoresTheBuiltinAlgorithm(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(SignatureBackend::class);

        SignatureAlgorithmFactory::registerAlgorithmFactory(
            C::SIG_RSA_SHA256,
            static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
                new PrivateKeyAgentRSA($key, $algId, $pkaBackend),
        );

        $factory = new SignatureAlgorithmFactory([]);
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $factory->getAlgorithm(C::SIG_RSA_SHA256, $cert));

        SignatureAlgorithmFactory::unregisterAlgorithmFactory(C::SIG_RSA_SHA256);

        // The class-string registration populated at initialization takes over again.
        $alg = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$pkey);
        $this->assertNotInstanceOf(PrivateKeyAgentRSA::class, $alg);
        $this->assertSame(C::SIG_RSA_SHA256, $alg->getAlgorithmId());
    }


    public function testUnregisterAlgorithmFactoryForUnknownAlgorithmIsANoop(): void
    {
        SignatureAlgorithmFactory::unregisterAlgorithmFactory(C::SIG_RSA_SHA512);

        $factory = new SignatureAlgorithmFactory([]);
        $this->assertSame(
            C::SIG_RSA_SHA512,
            $factory->getAlgorithm(C::SIG_RSA_SHA512, self::$pkey)->getAlgorithmId(),
        );
    }


    public function testClearAlgorithmFactoriesRemovesEveryRegistration(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(SignatureBackend::class);
        $closure = static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
            new PrivateKeyAgentRSA($key, $algId, $pkaBackend);

        SignatureAlgorithmFactory::registerAlgorithmFactory(C::SIG_RSA_SHA256, $closure);
        SignatureAlgorithmFactory::registerAlgorithmFactory(C::SIG_RSA_SHA384, $closure);

        $factory = new SignatureAlgorithmFactory([]);
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $factory->getAlgorithm(C::SIG_RSA_SHA256, $cert));
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $factory->getAlgorithm(C::SIG_RSA_SHA384, $cert));

        SignatureAlgorithmFactory::clearAlgorithmFactories();

        $this->assertNotInstanceOf(
            PrivateKeyAgentRSA::class,
            $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$pkey),
        );
        $this->assertNotInstanceOf(
            PrivateKeyAgentRSA::class,
            $factory->getAlgorithm(C::SIG_RSA_SHA384, self::$pkey),
        );
    }


    public function testBlacklistStillAppliesAfterReregistration(): void
    {
        $pkaBackend = $this->createStub(SignatureBackend::class);
        $closure = static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
            new PrivateKeyAgentRSA($key, $algId, $pkaBackend);

        SignatureAlgorithmFactory::registerAlgorithmFactory(C::SIG_RSA_SHA256, $closure);
        SignatureAlgorithmFactory::unregisterAlgorithmFactory(C::SIG_RSA_SHA256);
        SignatureAlgorithmFactory::registerAlgorithmFactory(C::SIG_RSA_SHA256, $closure);

        $factory = new SignatureAlgorithmFactory([C::SIG_RSA_SHA256]);
        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$pkey);
    }
}
