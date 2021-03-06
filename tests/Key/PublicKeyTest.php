<?php

namespace SimpleSAML\XMLSecurity\Key;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\PublicKey;

/**
 * Tests for SimpleSAML\XMLSecurity\Key\PublicKey.
 *
 * @package SimpleSAML\XMLSecurity\Key
 */
final class PublicKeyTest extends TestCase
{
    /** @var resource */
    protected $pubKey;

    /** @var string */
    protected string $f;


    /**
     * Initialize the test by loading the file ourselves.
     */
    protected function setUp(): void
    {
        $this->f = file_get_contents('tests/pubkey.pem');
        $this->pubKey = openssl_pkey_get_details(openssl_pkey_get_public($this->f));
    }

    /**
     * Cover basic creation and retrieval.
     */
    public function testCreation(): void
    {
        $k = new PublicKey($this->f);
        $keyDetails = openssl_pkey_get_details($k->get());
        $this->assertEquals($this->pubKey['key'], $keyDetails['key']);
    }


    /**
     * Test creation from a file containing the PEM-encoded public key.
     */
    public function testFromFile(): void
    {
        $k = PublicKey::fromFile('tests/pubkey.pem');
        $keyDetails = openssl_pkey_get_details($k->get());
        $this->assertEquals($this->pubKey['key'], $keyDetails['key']);
    }


    /**
     * Test failure to create key from missing file.
     */
    public function testFromMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        @PublicKey::fromFile('foo/bar');
    }


    /**
     * Test creation from the RSA public key details (modulus and exponent).
     */
    public function testFromDetails(): void
    {
        $k = PublicKey::fromDetails($this->pubKey['rsa']['n'], $this->pubKey['rsa']['e']);
        $keyDetails = openssl_pkey_get_details($k->get());
        $this->assertEquals($this->pubKey['key'], $keyDetails['key']);
    }
}
