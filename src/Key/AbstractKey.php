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
    /** @var mixed */
    protected mixed $key_material;


    /**
     * Build a new key with $key as its material.
     *
     * @param mixed $key The associated key material.
     */
    public function __construct(mixed $key)
    {
        $this->key_material = $key;
    }


    /**
     * Return the key material associated with this key.
     *
     * @return mixed The key material.
     */
    public function get(): mixed
    {
        return $this->key_material;
    }
}
