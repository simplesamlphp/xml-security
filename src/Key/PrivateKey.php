<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;

use function openssl_pkey_export;
use function openssl_pkey_get_private;

/**
 * A class modeling private keys for their use in asymmetric algorithms.
 *
 * @package simplesamlphp/xml-security
 */
class PrivateKey extends AsymmetricKey
{
    /**
     * Create a new private key from the PEM-encoded key material.
     *
     * @param string $key The PEM-encoded key material.
     */
    final public function __construct(string $key)
    {
        parent::__construct($key);
    }


    /**
     * Get a new private key from a file.
     *
     * @param string $file The file where the PEM-encoded private key is stored.
     * @param string $passphrase An optional passphrase used to decrypt the given key material.
     *
     * @return static A new private key.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the file cannot be read.
     */
    public static function fromFile(string $file, string $passphrase = ''): static
    {
        openssl_pkey_export(openssl_pkey_get_private($file, $passphrase), $decrypted);
        return new static(PEM::fromString($decrypted)->string());
    }
}
