<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Key;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Exception\FileNotFoundException;

use function file_get_contents;

/**
 * A class representing an asymmetric key.
 *
 * This class can be extended to implement public or private keys.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AsymmetricKey extends AbstractKey
{
    /** @var resource */
    protected $key_material;


    /**
     * Read a key from a given file.
     *
     * @param string $file The path to a file where the key is stored.
     *
     * @return string The key material.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the given file cannot be read.
     */
    protected static function readFile(string $file): string
    {
        $key = file_get_contents($file);

        Assert::true(is_string($key), 'Cannot read key from file "' . $file . '"', FileNotFoundException::class);

        return $key;
    }
}
