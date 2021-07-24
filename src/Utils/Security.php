<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

use function count;
use function hash_equals;
use function in_array;
use function openssl_pkey_get_details;
use function serialize;
use function sha1;
use function str_pad;
use function str_replace;
use function strlen;
use function strval;
use function substr;
use function trim;
use function var_export;

/**
 * A collection of security-related functions.
 *
 * @package simplesamlphp/xml-security
 */
class Security
{
    /**
     * Compare two strings in constant time.
     *
     * This function allows us to compare two given strings without any timing side channels
     * leaking information about them.
     *
     * @param string $known The reference string.
     * @param string $user The user-provided string to test.
     *
     * @return bool True if both strings are equal, false otherwise.
     */
    public static function compareStrings(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }


    /**
     * Compute the hash for some data with a given algorithm.
     *
     * @param string $alg The identifier of the algorithm to use.
     * @param string $data The data to digest.
     * @param bool $encode Whether to bas64-encode the result or not. Defaults to true.
     *
     * @return string The (binary or base64-encoded) digest corresponding to the given data.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $alg is not a valid
     *   identifier of a supported digest algorithm.
     */
    public static function hash(string $alg, string $data, bool $encode = true): string
    {
        if (!array_key_exists($alg, Constants::$DIGEST_ALGORITHMS)) {
            throw new InvalidArgumentException('Unsupported digest method "' . $alg . '"');
        }

        $digest = hash(Constants::$DIGEST_ALGORITHMS[$alg], $data, true);
        if ($encode) {
            $digest = base64_encode($digest);
        }
        return $digest;
    }
}
