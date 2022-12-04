<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

use OpenSSLAsymmetricKey;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;

/**
 * A class representing an asymmetric key.
 *
 * This class can be extended to implement public or private keys.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AsymmetricKey implements KeyInterface
{
    /** @var \SimpleSAML\XMLSecurity\CryptoEncoding\PEM */
    protected PEM $material;


    /**
     * Build a new key with $key as its material.
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $key The associated key material.
     */
    public function __construct(PEM $key)
    {
        $this->material = $key;
    }


    /**
     * Return the key material associated with this key.
     *
     * @return string The key material.
     */
    public function getMaterial(): string
    {
        return $this->material->string();
    }


    /**
     * Return the raw PEM-object associated with this key.
     *
     * @return \SimpleSAML\XMLSecurity\CryptoEncoding\PEM The raw material.
     */
    public function getPEM(): PEM
    {
        return $this->material;
    }
}
