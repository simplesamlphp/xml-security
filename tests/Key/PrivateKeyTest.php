<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Key;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key\PrivateKey;

use function file_get_contents;
use function openssl_pkey_get_details;
use function openssl_pkey_get_private;

/**
 * Tests for SimpleSAML\XMLSecurity\Key\PrivateKey
 *
 * @package SimpleSAML\XMLSecurity\Key
 */
final class PrivateKeyTest extends TestCase
{
    /** @var array */
    protected $privKey = [];

    /** @var string */
    protected string $f;


    /**
     * Initialize the test by loading the file ourselves.
     */
    protected function setUp(): void
    {
        $this->f = file_get_contents('resources/keys/privkey.pem');
        $this->privKey = openssl_pkey_get_details(openssl_pkey_get_private($this->f));
    }


    /**
     * Cover basic creation and retrieval.
     */
    public function testCreation(): void
    {
        $k = new PrivateKey(PEM::fromString($this->f));
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($k->getMaterial()));
        $this->assertEquals($this->privKey['key'], $keyDetails['key']);
    }


    /**
     * Test creation from a file containing the PEM-encoded private key.
     */
    public function testFromFile(): void
    {
        $k = PrivateKey::fromFile('file://./resources/keys/privkey.pem');
        $keyDetails = openssl_pkey_get_details(openssl_pkey_get_private($k->getMaterial()));
        $this->assertEquals($this->privKey['key'], $keyDetails['key']);
    }
}
