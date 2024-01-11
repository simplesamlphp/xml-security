<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Key;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Exception\IOException;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function openssl_pkey_get_details;
use function openssl_pkey_get_public;

/**
 * Tests for SimpleSAML\XMLSecurity\Key\PublicKey.
 *
 * @package SimpleSAML\XMLSecurity\Key
 */
final class PublicKeyTest extends TestCase
{
    /** @var array */
    protected static $pubKey = [];

    /** @var string */
    protected static string $f;


    /**
     * Initialize the test by loading the file ourselves.
     */
    public static function setUpBeforeClass(): void
    {
        self::$f = PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        self::$pubKey = openssl_pkey_get_details(openssl_pkey_get_public(self::$f));
    }

    /**
     * Cover basic creation and retrieval.
     */
    public function testCreation(): void
    {
        $k = new PublicKey(PEM::fromString(self::$f));
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_public($k->getMaterial()));
        $this->assertEquals(self::$pubKey['key'], $keyDetails['key']);
    }


    /**
     * Test creation from a file containing the PEM-encoded public key.
     */
    public function testFromFile(): void
    {
        $k = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_public($k->getMaterial()));
        $this->assertEquals(self::$pubKey['key'], $keyDetails['key']);
    }


    /**
     * Test failure to create key from missing file.
     */
    public function testFromMissingFile(): void
    {
        $this->expectException(IOException::class);
        @PublicKey::fromFile('foo/bar');
    }


    /**
     * Test creation from the RSA public key details (modulus and exponent).
     */
    public function testFromDetails(): void
    {
        $k = PublicKey::fromDetails(self::$pubKey['rsa']['n'], self::$pubKey['rsa']['e']);
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_public($k->getMaterial()));
        $this->assertEquals(self::$pubKey['key'], $keyDetails['key']);
    }
}
