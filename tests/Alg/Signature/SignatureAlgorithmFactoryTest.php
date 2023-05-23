<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Signature;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\HMAC;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
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
}
