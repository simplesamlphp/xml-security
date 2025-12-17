<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Backend\HMAC;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

use function bin2hex;
use function hex2bin;

/**
 * Test for SimpleSAML\XMLSecurity\Backend\HMAC.
 *
 * @package SimpleSAML\XMLSecurity\Backend
 */
final class HMACTest extends TestCase
{
    public const string PLAINTEXT = "plaintext";

    public const string SIGNATURE = "61b85d9e800ed0eca556a304cc9e1ac7ae8eecb3";

    public const string SECRET = 'secret key';


    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $key;

    /** @var \SimpleSAML\XMLSecurity\Backend\HMAC */
    protected static HMAC $backend;


    /**
     * Initialize shared key.
     */
    public static function setUpBeforeClass(): void
    {
        self::$key = new SymmetricKey(self::SECRET);
        self::$backend = new HMAC();
    }


    /**
     * Test signing of messages.
     */
    public function testSign(): void
    {
        self::$backend->setDigestAlg(C::DIGEST_SHA1);
        $this->assertEquals(self::SIGNATURE, bin2hex(self::$backend->sign(self::$key, self::PLAINTEXT)));
    }


    /**
     * Test for wrong digests.
     */
    public function testSetUnknownDigest(): void
    {
        $this->expectException(InvalidArgumentException::class);
        self::$backend->setDigestAlg('foo');
    }


    /**
     * Test verification of signatures.
     */
    public function testVerify(): void
    {
        // test successful verification
        self::$backend->setDigestAlg(C::DIGEST_SHA1);
        $this->assertTrue(self::$backend->verify(self::$key, self::PLAINTEXT, hex2bin(self::SIGNATURE)));

        // test failure to verify with different plaintext
        $this->assertFalse(self::$backend->verify(self::$key, 'foo', hex2bin(self::SIGNATURE)));

        // test failure to verify with different signature
        $this->assertFalse(self::$backend->verify(
            self::$key,
            self::PLAINTEXT,
            hex2bin('12345678901234567890abcdefabcdef12345678'),
        ));

        // test failure to verify with wrong key
        $key = new SymmetricKey('wrong secret');
        $this->assertFalse(self::$backend->verify($key, self::PLAINTEXT, hex2bin(self::SIGNATURE)));

        // test failure to verify with wrong digest algorithm
        self::$backend->setDigestAlg(C::DIGEST_RIPEMD160);
        $this->assertFalse(self::$backend->verify(self::$key, self::PLAINTEXT, hex2bin(self::SIGNATURE)));
    }
}
