<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

use OpenSSLAsymmetricKey;

/**
 * A class representing a key.
 *
 * This class can be extended in order to implement specific types of keys.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractKey
{
    /** @var OpenSSLAsymmetricKey|string */
    protected OpenSSLAsymmetricKey|string $key_material;


    /**
     * Build a new key with $key as its material.
     *
     * @param OpenSSLAsymmetricKey|string $key The associated key material.
     */
    public function __construct(OpenSSLAsymmetricKey|string $key)
    {
        $this->key_material = $key;
    }


    /**
     * Return the key material associated with this key.
     *
     * @return OpenSSLAsymmetricKey|string The key material.
     */
    public function get(): OpenSSLAsymmetricKey|string
    {
        return $this->key_material;
    }
}
