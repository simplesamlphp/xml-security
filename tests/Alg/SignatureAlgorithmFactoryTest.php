<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\HMAC;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Tests for SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureAlgorithmFactoryTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected SymmetricKey $skey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected PublicKey $pkey;


    public function setUp(): void
    {
        $this->skey = SymmetricKey::generate(16);
        $this->pkey = PublicKey::fromFile('tests/pubkey.pem');
    }


    /**
     * Test obtaining the digest algorithm from a signature algorithm.
     */
    public function testGetDigestAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([]);
        $hmac = [
            Constants::SIG_HMAC_SHA1      => Constants::DIGEST_SHA1,
            Constants::SIG_HMAC_SHA224    => Constants::DIGEST_SHA224,
            Constants::SIG_HMAC_SHA256    => Constants::DIGEST_SHA256,
            Constants::SIG_HMAC_SHA384    => Constants::DIGEST_SHA384,
            Constants::SIG_HMAC_SHA512    => Constants::DIGEST_SHA512,
            Constants::SIG_HMAC_RIPEMD160 => Constants::DIGEST_RIPEMD160,
        ];

        $rsa = [
            Constants::SIG_RSA_SHA1      => Constants::DIGEST_SHA1,
            Constants::SIG_RSA_SHA224    => Constants::DIGEST_SHA224,
            Constants::SIG_RSA_SHA256    => Constants::DIGEST_SHA256,
            Constants::SIG_RSA_SHA384    => Constants::DIGEST_SHA384,
            Constants::SIG_RSA_SHA512    => Constants::DIGEST_SHA512,
            Constants::SIG_RSA_RIPEMD160 => Constants::DIGEST_RIPEMD160,
        ];

        foreach ($hmac as $signature => $digest) {
            $alg = $factory->getAlgorithm($signature, $this->skey);
            $this->assertEquals($digest, $alg->getDigest());
        }

        foreach ($rsa as $signature => $digest) {
            $alg = $factory->getAlgorithm($signature, $this->pkey);
            $this->assertEquals($digest, $alg->getDigest());
        }
    }


    /**
     * Test for unsupported algorithms.
     */
    public function testGetUnknownAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([]);
        $this->expectException(RuntimeException::class);
        $factory->getAlgorithm('Unknown alg', $this->skey);
    }


    /**
     * Test for blacklisted algorithms.
     */
    public function testBlacklistedAlgorithm(): void
    {
        $factory = new SignatureAlgorithmFactory([Constants::SIG_RSA_SHA1]);
        $this->assertInstanceOf(
            HMAC::class,
            $factory->getAlgorithm(Constants::SIG_HMAC_SHA1, $this->skey)
        );

        $this->expectException(InvalidArgumentException::class);
        $factory->getAlgorithm(Constants::SIG_RSA_SHA1, $this->pkey);
    }
}
