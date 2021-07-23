<?php

namespace SimpleSAML\XMLSecurity\Key;

use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

use function openssl_pkey_get_private;

/**
 * A class modeling private keys for their use in asymmetric algorithms.
 *
 * @package SimpleSAML\XMLSecurity\Key
 */
class PrivateKey extends AsymmetricKey
{
    /**
     * Create a new private key from the PEM-encoded key material.
     *
     * @param string $key The PEM-encoded key material.
     * @param string $passphrase An optional passphrase used to decrypt the given key material.
     */
    public function __construct(string $key, string $passphrase = "")
    {
        parent::__construct(openssl_pkey_get_private($key, $passphrase));
    }


    /**
     * Get a new private key from a file.
     *
     * @param string $file The file where the PEM-encoded private key is stored.
     * @param string $passphrase An optional passphrase used to decrypt the given key material.
     *
     * @return \SimpleSAML\XMLSecurity\Key\PrivateKey A new private key.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the file cannot be read.
     */
    public static function fromFile(string $file, string $passphrase = ""): PrivateKey
    {
        return new static(self::readFile($file), $passphrase);
    }
}
