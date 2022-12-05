<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

use OpenSSLAsymmetricKey;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;

use function openssl_error_string;
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
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $key The PEM-encoded key material.
     */
    final public function __construct(PEM $key)
    {
        Assert::oneOf(
            $key->type(),
            [PEM::TYPE_PRIVATE_KEY, PEM::TYPE_RSA_PRIVATE_KEY],
            "PEM structure has the wrong type %s."
        );

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
        if (($key = openssl_pkey_get_private($file, $passphrase)) === false) {
            throw new RuntimeException('Failed to read key: ' . openssl_error_string());
        }

        // Some OpenSSL functions will add errors to the list even if they succeed
        while (openssl_error_string() !== false);

        if (openssl_pkey_export($key, $decrypted) === false) {
            throw new RuntimeException('Failed to export key: ' . openssl_error_string());
        }

        // Some OpenSSL functions will add errors to the list even if they succeed
        while (openssl_error_string() !== false);

        return new static(PEM::fromString($decrypted));
    }
}
