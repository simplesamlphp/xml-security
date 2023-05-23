<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\KeyTransport;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\RSA;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
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
}
