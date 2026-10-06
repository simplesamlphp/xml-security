<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\KeyTransport;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\PrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\RSA;
use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportALgorithmFactory
 *
 * @package simplesamlphp/xml-security
 */
class KeyTransportAlgorithmFactoryTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $pkey;


    public static function setUpBeforeClass(): void
    {
        self::$pkey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
    }


    protected function tearDown(): void
    {
        $ref = new \ReflectionClass(KeyTransportAlgorithmFactory::class);
        $ref->getProperty('cache')->setValue(null, []);
        $ref->getProperty('initialized')->setValue(null, false);
        KeyTransportAlgorithmFactory::clearAlgorithmFactories();
    }


    /**
     * Test for unsupported algorithms.
     */
    public function testGetUnknownAlgorithm(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $this->expectException(UnsupportedAlgorithmException::class);
        $factory->getAlgorithm('Unsupported algorithm identifier', self::$pkey);
    }


    /**
     * Test the default blacklisted algorithms.
     */
    public function testDefaultBlacklistedAlgorithm(): void
    {
        $factory = new KeyTransportAlgorithmFactory();
        $algorithm = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pkey);
        $this->assertInstanceOf(RSA::class, $algorithm);
        $this->assertEquals(C::KEY_TRANSPORT_OAEP, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey);
        $this->assertInstanceOf(RSA::class, $algorithm);
        $this->assertEquals(C::KEY_TRANSPORT_OAEP_MGF1P, $algorithm->getAlgorithmId());

        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$pkey);
    }


    /**
     * Test for manually blacklisted algorithms.
     */
    public function testBlacklistedAlgorithm(): void
    {
        $factory = new KeyTransportAlgorithmFactory([C::KEY_TRANSPORT_OAEP_MGF1P]);
        $algorithm = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pkey);
        $this->assertInstanceOf(RSA::class, $algorithm);
        $this->assertEquals(C::KEY_TRANSPORT_OAEP, $algorithm->getAlgorithmId());
        $this->assertEquals(self::$pkey, $algorithm->getKey());

        $algorithm = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$pkey);
        $this->assertInstanceOf(RSA::class, $algorithm);
        $this->assertEquals(C::KEY_TRANSPORT_RSA_1_5, $algorithm->getAlgorithmId());

        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey);
    }


    public function testRegisterAlgorithmFactoryClosureYieldsWrapperOnGetAlgorithm(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(EncryptionBackend::class);

        KeyTransportAlgorithmFactory::registerAlgorithmFactory(
            C::KEY_TRANSPORT_OAEP_MGF1P,
            static function (KeyInterface $key, string $algId) use ($pkaBackend): PrivateKeyAgentRSA {
                return new PrivateKeyAgentRSA($key, $algId, $pkaBackend);
            },
        );

        $factory = new KeyTransportAlgorithmFactory([]);
        $alg = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $cert);

        $this->assertSame(C::KEY_TRANSPORT_OAEP_MGF1P, $alg->getAlgorithmId());
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $alg);
    }


    public function testBlacklistBlocksClosureRegisteredAlgorithm(): void
    {
        $pkaBackend = $this->createStub(EncryptionBackend::class);

        KeyTransportAlgorithmFactory::registerAlgorithmFactory(
            C::KEY_TRANSPORT_OAEP_MGF1P,
            static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
                new PrivateKeyAgentRSA($key, $algId, $pkaBackend),
        );

        $factory = new KeyTransportAlgorithmFactory([C::KEY_TRANSPORT_OAEP_MGF1P]);
        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey);
    }


    public function testUnregisterAlgorithmFactoryRestoresTheBuiltinAlgorithm(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(EncryptionBackend::class);

        KeyTransportAlgorithmFactory::registerAlgorithmFactory(
            C::KEY_TRANSPORT_OAEP_MGF1P,
            static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
                new PrivateKeyAgentRSA($key, $algId, $pkaBackend),
        );

        $factory = new KeyTransportAlgorithmFactory([]);
        $this->assertInstanceOf(
            PrivateKeyAgentRSA::class,
            $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $cert),
        );

        KeyTransportAlgorithmFactory::unregisterAlgorithmFactory(C::KEY_TRANSPORT_OAEP_MGF1P);

        // The class-string registration populated at initialization takes over again.
        $alg = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey);
        $this->assertInstanceOf(RSA::class, $alg);
        $this->assertSame(C::KEY_TRANSPORT_OAEP_MGF1P, $alg->getAlgorithmId());
    }


    public function testUnregisterAlgorithmFactoryForUnknownAlgorithmIsANoop(): void
    {
        KeyTransportAlgorithmFactory::unregisterAlgorithmFactory(C::KEY_TRANSPORT_OAEP);

        $factory = new KeyTransportAlgorithmFactory([]);
        $this->assertInstanceOf(RSA::class, $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pkey));
    }


    public function testClearAlgorithmFactoriesRemovesEveryRegistration(): void
    {
        $cert = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->createStub(EncryptionBackend::class);
        $closure = static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
            new PrivateKeyAgentRSA($key, $algId, $pkaBackend);

        KeyTransportAlgorithmFactory::registerAlgorithmFactory(C::KEY_TRANSPORT_OAEP, $closure);
        KeyTransportAlgorithmFactory::registerAlgorithmFactory(C::KEY_TRANSPORT_OAEP_MGF1P, $closure);

        $factory = new KeyTransportAlgorithmFactory([]);
        $this->assertInstanceOf(PrivateKeyAgentRSA::class, $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, $cert));
        $this->assertInstanceOf(
            PrivateKeyAgentRSA::class,
            $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, $cert),
        );

        KeyTransportAlgorithmFactory::clearAlgorithmFactories();

        $this->assertInstanceOf(RSA::class, $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pkey));
        $this->assertInstanceOf(RSA::class, $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey));
    }


    public function testBlacklistStillAppliesAfterReregistration(): void
    {
        $pkaBackend = $this->createStub(EncryptionBackend::class);
        $closure = static fn(KeyInterface $key, string $algId): PrivateKeyAgentRSA =>
            new PrivateKeyAgentRSA($key, $algId, $pkaBackend);

        KeyTransportAlgorithmFactory::registerAlgorithmFactory(C::KEY_TRANSPORT_OAEP_MGF1P, $closure);
        KeyTransportAlgorithmFactory::unregisterAlgorithmFactory(C::KEY_TRANSPORT_OAEP_MGF1P);
        KeyTransportAlgorithmFactory::registerAlgorithmFactory(C::KEY_TRANSPORT_OAEP_MGF1P, $closure);

        $factory = new KeyTransportAlgorithmFactory([C::KEY_TRANSPORT_OAEP_MGF1P]);
        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pkey);
    }
}
