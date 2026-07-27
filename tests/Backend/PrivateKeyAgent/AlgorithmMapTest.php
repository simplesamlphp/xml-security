<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\AlgorithmMap;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;

/**
 * Tests for AlgorithmMap.
 *
 * @package simplesamlphp/xml-security
 */
final class AlgorithmMapTest extends TestCase
{
    private const string MGF1_SHA1   = 'http://www.w3.org/2009/xmlenc11#mgf1sha1';

    private const string MGF1_SHA224 = 'http://www.w3.org/2009/xmlenc11#mgf1sha224';

    private const string MGF1_SHA256 = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';

    private const string MGF1_SHA384 = 'http://www.w3.org/2009/xmlenc11#mgf1sha384';

    private const string MGF1_SHA512 = 'http://www.w3.org/2009/xmlenc11#mgf1sha512';


    /** @return array<string, array{string, string}> */
    public static function signingPositiveProvider(): array
    {
        return [
            'sha1'   => [C::DIGEST_SHA1,   'rsa-pkcs1-v1_5-sha1'],
            'sha256' => [C::DIGEST_SHA256,  'rsa-pkcs1-v1_5-sha256'],
            'sha384' => [C::DIGEST_SHA384,  'rsa-pkcs1-v1_5-sha384'],
            'sha512' => [C::DIGEST_SHA512,  'rsa-pkcs1-v1_5-sha512'],
        ];
    }


    #[DataProvider('signingPositiveProvider')]
    public function testSigningAlgorithmPositive(string $digest, string $expected): void
    {
        $this->assertSame($expected, AlgorithmMap::getSigningAlgorithm($digest));
    }


    /** @return array<string, array{string}> */
    public static function signingNegativeProvider(): array
    {
        return [
            'sha224'  => [C::DIGEST_SHA224],
            'unknown' => ['http://example.com/unknown-hash'],
        ];
    }


    #[DataProvider('signingNegativeProvider')]
    public function testSigningAlgorithmNegative(string $digest): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        AlgorithmMap::getSigningAlgorithm($digest);
    }


    /** @return array<string, array{string, string|null, string|null, string}> */
    public static function keyTransportPositiveProvider(): array
    {
        return [
            'rsa-1_5' => [C::KEY_TRANSPORT_RSA_1_5, null, null, 'rsa-pkcs1-v1_5'],
            'mgf1p no params' => [C::KEY_TRANSPORT_OAEP_MGF1P, null, null, 'rsa-pkcs1-oaep-mgf1-sha1'],
            'mgf1p explicit sha1 digest' => [
                C::KEY_TRANSPORT_OAEP_MGF1P, C::DIGEST_SHA1, null, 'rsa-pkcs1-oaep-mgf1-sha1',
            ],
            'mgf1p explicit sha1 digest and mgf' => [
                C::KEY_TRANSPORT_OAEP_MGF1P, C::DIGEST_SHA1, self::MGF1_SHA1, 'rsa-pkcs1-oaep-mgf1-sha1',
            ],
            'oaep both absent (default sha256)' => [
                C::KEY_TRANSPORT_OAEP, null, null, 'rsa-pkcs1-oaep-mgf1-sha256',
            ],
            'oaep sha1/mgf1sha1' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA1, self::MGF1_SHA1, 'rsa-pkcs1-oaep-mgf1-sha1',
            ],
            'oaep sha224/mgf1sha224' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA224, self::MGF1_SHA224, 'rsa-pkcs1-oaep-mgf1-sha224',
            ],
            'oaep sha256/mgf1sha256' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA256, self::MGF1_SHA256, 'rsa-pkcs1-oaep-mgf1-sha256',
            ],
            'oaep sha384/mgf1sha384' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA384, self::MGF1_SHA384, 'rsa-pkcs1-oaep-mgf1-sha384',
            ],
            'oaep sha512/mgf1sha512' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA512, self::MGF1_SHA512, 'rsa-pkcs1-oaep-mgf1-sha512',
            ],
        ];
    }


    #[DataProvider('keyTransportPositiveProvider')]
    public function testKeyTransportAlgorithmPositive(
        string $cipherUri,
        ?string $digestAlg,
        ?string $mgf,
        string $expected,
    ): void {
        $this->assertSame($expected, AlgorithmMap::getKeyTransportAlgorithm($cipherUri, $digestAlg, $mgf));
    }


    /** @return array<string, array{string, string|null, string|null}> */
    public static function keyTransportNegativeProvider(): array
    {
        return [
            'unknown cipher' => ['http://example.com/unknown', null, null],
            'mgf1p with sha256 digest' => [C::KEY_TRANSPORT_OAEP_MGF1P, C::DIGEST_SHA256, null],
            'mgf1p with mgf1sha256' => [C::KEY_TRANSPORT_OAEP_MGF1P, null, self::MGF1_SHA256],
            'mgf1p with sha256 digest + mgf1sha256' => [
                C::KEY_TRANSPORT_OAEP_MGF1P, C::DIGEST_SHA256, self::MGF1_SHA256,
            ],
            'oaep digest only (no mgf)' => [C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA256, null],
            'oaep mgf only (no digest)' => [C::KEY_TRANSPORT_OAEP, null, self::MGF1_SHA256],
            'oaep mismatched sha256/mgf1sha512' => [C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA256, self::MGF1_SHA512],
            'oaep unknown digest' => [
                C::KEY_TRANSPORT_OAEP, 'http://example.com/hash', self::MGF1_SHA256,
            ],
            'oaep unknown mgf' => [
                C::KEY_TRANSPORT_OAEP, C::DIGEST_SHA256, 'http://example.com/mgf',
            ],
        ];
    }


    #[DataProvider('keyTransportNegativeProvider')]
    public function testKeyTransportAlgorithmNegative(
        string $cipherUri,
        ?string $digestAlg,
        ?string $mgf,
    ): void {
        $this->expectException(UnsupportedAlgorithmException::class);
        AlgorithmMap::getKeyTransportAlgorithm($cipherUri, $digestAlg, $mgf);
    }
}
