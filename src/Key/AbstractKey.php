<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

/**
 * A class representing a key.
 *
 * This class can be extended in order to implement specific types of keys.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractKey
{
    /** @var string */
    protected string $material;


    /**
     * Build a new key with $key as its material.
     *
     * @param string $key The associated key material.
     */
    public function __construct(string $key)
    {
        $this->material = $key;
    }


    /**
     * Return the key material associated with this key.
     *
     * @return string The key material.
     */
    public function get(): string
    {
        return $this->material;
    }
}
