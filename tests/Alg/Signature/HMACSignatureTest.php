<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Alg\Signature;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

use function bin2hex;
use function hex2bin;

/**
 * Tests for SimpleSAML\XMLSecurity\Alg\Signature\HMAC.
 *
 * @package SimpleSAML\Signature
 */
final class HMACSignatureTest extends TestCase
{
    protected const string PLAINTEXT = 'plaintext';

    protected const string SECRET = 'de54fbd0f10c34df6e800b11043024fa';


    /** @var \SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory */
    protected static SignatureAlgorithmFactory $factory;

    /** @var \SimpleSAML\XMLSecurity\Key\SymmetricKey */
    protected static SymmetricKey $key;


    public static function setUpBeforeClass(): void
    {
        self::$factory = new SignatureAlgorithmFactory([]);
        self::$key = new SymmetricKey(self::SECRET);
    }


    /**
     * Test that signing works.
     */
    public function testSign(): void
    {
        // test HMAC-SHA1
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA1, self::$key);
        $this->assertEquals('655c3b4277b39f31dedf5adc7f4cc9f07da7102c', bin2hex($hmac->sign(self::PLAINTEXT)));

        // test HMAC-SHA224
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA224, self::$key);
        $this->assertEquals(
            '645405ccc725e10022e5a89e98cc33db07c0cd89ba78c21caf931f40',
            bin2hex($hmac->sign(self::PLAINTEXT)),
        );

        // test HMAC-SHA256
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA256, self::$key);
        $this->assertEquals(
            '721d8385785a3d4c8d16c7b4a96b343728a11e221656e6dd9502d540d4e87ef2',
            bin2hex($hmac->sign(self::PLAINTEXT)),
        );

        // test HMAC-SHA384
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA384, self::$key);
        $this->assertEquals(
            'b3ad2e39a057fd7a952cffd503d30eca295c6698dc23ddf0bebf98631a0162da0db0105db156a220dec78cebaf2c202c',
            bin2hex($hmac->sign(self::PLAINTEXT)),
        );

        // test HMAC-SHA512
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA512, self::$key);
        $this->assertEquals(
            '9cc73c95f564a142b28340cf6e1d6b509a9e97dab6577e5d0199760a858105185252e203b6b096ad24708a2b7e34a0f506776d8' .
            '8e2f47fff055fc51342b69cdc',
            bin2hex($hmac->sign(self::PLAINTEXT)),
        );

        // test HMAC-RIPEMD160
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_RIPEMD160, self::$key);
        $this->assertEquals('a9fd77b68644464d08be0ba2cd998eab3e2a7b1d', bin2hex($hmac->sign(self::PLAINTEXT)));
    }


    /**
     * Test that signature verification works.
     */
    public function testVerify(): void
    {
        // test HMAC-SHA1
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA1, self::$key);
        $this->assertTrue($hmac->verify(self::PLAINTEXT, hex2bin('655c3b4277b39f31dedf5adc7f4cc9f07da7102c')));

        // test HMAC-SHA224
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA224, self::$key);
        $this->assertTrue($hmac->verify(
            self::PLAINTEXT,
            hex2bin('645405ccc725e10022e5a89e98cc33db07c0cd89ba78c21caf931f40'),
        ));

        // test HMAC-SHA256
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA256, self::$key);
        $this->assertTrue($hmac->verify(
            self::PLAINTEXT,
            hex2bin('721d8385785a3d4c8d16c7b4a96b343728a11e221656e6dd9502d540d4e87ef2'),
        ));

        // test HMAC-SHA384
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA384, self::$key);
        $this->assertTrue($hmac->verify(
            self::PLAINTEXT,
            hex2bin('b3ad2e39a057fd7a952cffd503d30eca295c6698dc23ddf0bebf98631a0162da0db0105db156a220dec78cebaf2c202c'),
        ));

        // test HMAC-SHA512
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA512, self::$key);
        $this->assertTrue($hmac->verify(
            self::PLAINTEXT,
            hex2bin(
                '9cc73c95f564a142b28340cf6e1d6b509a9e97dab6577e5d0199760a858105185252e203b6b096ad24708a2b7e34a0f5067' .
                '76d88e2f47fff055fc51342b69cdc',
            ),
        ));

        // test HMAC-RIPEMD160
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_RIPEMD160, self::$key);
        $this->assertTrue($hmac->verify(self::PLAINTEXT, hex2bin('a9fd77b68644464d08be0ba2cd998eab3e2a7b1d')));
    }


    /**
     * Test that signature verification fails properly.
     */
    public function testVerificationFailure(): void
    {
        // test wrong plaintext
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA1, self::$key);
        $this->assertFalse($hmac->verify(self::PLAINTEXT . '.', hex2bin('655c3b4277b39f31dedf5adc7f4cc9f07da7102c')));

        // test wrong signature
        $this->assertFalse($hmac->verify(self::PLAINTEXT, hex2bin('655c3b4277b39f31dedf5adc7f4cc9f07da7102d')));

        // test wrong key
        $wrongKey = new SymmetricKey('de54fbd0f10c34df6e800b11043024fb');
        $hmac = self::$factory->getAlgorithm(C::SIG_HMAC_SHA1, $wrongKey);
        $this->assertFalse($hmac->verify(self::PLAINTEXT, hex2bin('655c3b4277b39f31dedf5adc7f4cc9f07da7102c')));
    }
}
