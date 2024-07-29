<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Encryption;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Encryption\AES;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\Encryption\TripleDES;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Tests for \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory.
 *
 * @package simplesamlphp/xml-security
 */
class EncryptionAlgorithmFactoryTest extends TestCase
{
    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $skey;


    public static function setUpBeforeClass(): void
    {
        self::$skey = SymmetricKey::generate(16);
    }


    /**
     * Test for unsupported algorithms.
     */
    public function testGetUnknownAlgorithm(): void
    {
        $factory = new EncryptionAlgorithmFactory();
        $this->expectException(UnsupportedAlgorithmException::class);
        $factory->getAlgorithm('Unsupported algorithm identifier', self::$skey);
    }


    /**
     * Test the default blacklisted algorithms.
     */
    public function testDefaultBlacklistedAlgorithms(): void
    {
        $factory = new EncryptionAlgorithmFactory();

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES128, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES128, $algorithm->getAlgorithmId());
        $this->assertEquals(self::$skey, $algorithm->getKey());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES128_GCM, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES128_GCM, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES192, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES192, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES192_GCM, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES192_GCM, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES256, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES256, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES256_GCM, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES256_GCM, $algorithm->getAlgorithmId());

        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::BLOCK_ENC_3DES, self::$skey);
    }


    /**
     * Test for manually blacklisted algorithms.
     */
    public function testBlacklistedAlgorithm(): void
    {
        $factory = new EncryptionAlgorithmFactory([C::BLOCK_ENC_AES256_GCM]);

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_3DES, self::$skey);
        $this->assertInstanceOf(TripleDES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_3DES, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES128, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES128, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES128_GCM, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES128_GCM, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES192, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES192, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES192_GCM, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES192_GCM, $algorithm->getAlgorithmId());

        $algorithm = $factory->getAlgorithm(C::BLOCK_ENC_AES256, self::$skey);
        $this->assertInstanceOf(AES::class, $algorithm);
        $this->assertEquals(C::BLOCK_ENC_AES256, $algorithm->getAlgorithmId());

        $this->expectException(BlacklistedAlgorithmException::class);
        $factory->getAlgorithm(C::BLOCK_ENC_AES256_GCM, self::$skey);
    }
}
