<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use Error;
use Exception;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;

use function bin2hex;
use function random_bytes;
use function substr;

/**
 * A collection of utilities to generate cryptographically-secure random data.
 *
 * @package SimpleSAML\XMLSecurity\Utils
 */
class Random
{
    /**
     * Generate a given amount of cryptographically secure random bytes.
     *
     * @param positive-int $length The amount of bytes required.
     *
     * @return string A random string of $length length.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException
     *   If $length is not an integer greater than zero.
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException
     *   If no appropriate sources of cryptographically secure random generators are available.
     */
    public static function generateRandomBytes(int $length): string
    {
        Assert::positiveInteger(
            $length,
            'Invalid length received to generate random bytes.',
            InvalidArgumentException::class
        );

        try {
            return random_bytes($length);
        } catch (Error) {
            throw new InvalidArgumentException('Invalid length received to generate random bytes.');
        } catch (Exception) {
            throw new RuntimeException(
                'Cannot generate random bytes, no cryptographically secure random generator available.',
            );
        }
    }
}
