<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;

/**
 * Internal mapping tables between XMLSec algorithm URIs and PKA agent algorithm identifiers.
 *
 * MGF URI identifiers are defined locally here; do not add them to Constants.php.
 *
 * @package simplesamlphp/xml-security
 */
final class AlgorithmMap
{
    // MGF URI identifiers (xmlenc11 namespace, not in Constants.php)
    private const string MGF1_SHA1   = 'http://www.w3.org/2009/xmlenc11#mgf1sha1';

    private const string MGF1_SHA224 = 'http://www.w3.org/2009/xmlenc11#mgf1sha224';

    private const string MGF1_SHA256 = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';

    private const string MGF1_SHA384 = 'http://www.w3.org/2009/xmlenc11#mgf1sha384';

    private const string MGF1_SHA512 = 'http://www.w3.org/2009/xmlenc11#mgf1sha512';


    /**
     * Map a digest URI to the corresponding PKA signing algorithm identifier.
     *
     * @param string $digestAlg The XMLSec digest algorithm URI.
     *
     * @return string The agent algorithm identifier (e.g. 'rsa-pkcs1-v1_5-sha256').
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException For unsupported/unknown digests.
     */
    public static function getSigningAlgorithm(string $digestAlg): string
    {
        return match ($digestAlg) {
            C::DIGEST_SHA1   => 'rsa-pkcs1-v1_5-sha1',
            C::DIGEST_SHA256 => 'rsa-pkcs1-v1_5-sha256',
            C::DIGEST_SHA384 => 'rsa-pkcs1-v1_5-sha384',
            C::DIGEST_SHA512 => 'rsa-pkcs1-v1_5-sha512',
            // SHA-224 signing is not supported by the agent
            default => throw new UnsupportedAlgorithmException(
                sprintf('Digest algorithm \'%s\' is not supported for PKA signing.', $digestAlg),
            ),
        };
    }


    /**
     * Map a key-transport cipher URI plus optional OAEP digest/MGF to a PKA decryption algorithm identifier.
     *
     * @param string      $cipherUri The XMLSec key-transport algorithm URI.
     * @param string|null $digestAlg The OAEP digest algorithm URI, or null for algorithm default.
     * @param string|null $mgf       The MGF URI, or null for algorithm default.
     *
     * @return string The agent algorithm identifier (e.g. 'rsa-pkcs1-oaep-mgf1-sha256').
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException For unsupported combinations.
     */
    public static function getKeyTransportAlgorithm(
        string $cipherUri,
        ?string $digestAlg = null,
        ?string $mgf = null,
    ): string {
        if ($cipherUri === C::KEY_TRANSPORT_RSA_1_5) {
            return 'rsa-pkcs1-v1_5';
        }

        if ($cipherUri === C::KEY_TRANSPORT_OAEP_MGF1P) {
            return self::mapOaepMgf1p($digestAlg, $mgf);
        }

        if ($cipherUri === C::KEY_TRANSPORT_OAEP) {
            return self::mapOaep($digestAlg, $mgf);
        }

        throw new UnsupportedAlgorithmException(
            sprintf('Key-transport cipher \'%s\' is not supported by the PKA backend.', $cipherUri),
        );
    }


    /**
     * Map xmlenc#rsa-oaep-mgf1p (fixed SHA-1 MGF1) + optional digest to an agent algorithm.
     */
    private static function mapOaepMgf1p(?string $digestAlg, ?string $mgf): string
    {
        // mgf1p fixes MGF1-SHA-1; any explicit non-SHA-1 mgf is unsupported
        if ($mgf !== null && $mgf !== self::MGF1_SHA1) {
            throw new UnsupportedAlgorithmException(
                sprintf(
                    'rsa-oaep-mgf1p only supports MGF1-SHA-1; \'%s\' is not supported.',
                    $mgf,
                ),
            );
        }

        // Only SHA-1 digest (or absent/null = default SHA-1) is accepted for mgf1p
        if ($digestAlg !== null && $digestAlg !== C::DIGEST_SHA1) {
            throw new UnsupportedAlgorithmException(
                sprintf(
                    'rsa-oaep-mgf1p with digest \'%s\' is not supported; only SHA-1 is allowed.',
                    $digestAlg,
                ),
            );
        }

        return 'rsa-pkcs1-oaep-mgf1-sha1';
    }


    /**
     * Map xmlenc11#rsa-oaep + digest/MGF pair to an agent algorithm.
     */
    private static function mapOaep(?string $digestAlg, ?string $mgf): string
    {
        // Both absent → default variant SHA-256/MGF1-SHA-256
        if ($digestAlg === null && $mgf === null) {
            return 'rsa-pkcs1-oaep-mgf1-sha256';
        }

        // Exactly one present → unsupported (spec: both or neither)
        if (($digestAlg === null) !== ($mgf === null)) {
            throw new UnsupportedAlgorithmException(
                'rsa-oaep requires both digest and MGF to be set, or neither.',
            );
        }

        // Both present: validate the digest↔mgf pair
        return match (true) {
            $digestAlg === C::DIGEST_SHA1   && $mgf === self::MGF1_SHA1   => 'rsa-pkcs1-oaep-mgf1-sha1',
            $digestAlg === C::DIGEST_SHA224 && $mgf === self::MGF1_SHA224 => 'rsa-pkcs1-oaep-mgf1-sha224',
            $digestAlg === C::DIGEST_SHA256 && $mgf === self::MGF1_SHA256 => 'rsa-pkcs1-oaep-mgf1-sha256',
            $digestAlg === C::DIGEST_SHA384 && $mgf === self::MGF1_SHA384 => 'rsa-pkcs1-oaep-mgf1-sha384',
            $digestAlg === C::DIGEST_SHA512 && $mgf === self::MGF1_SHA512 => 'rsa-pkcs1-oaep-mgf1-sha512',
            default => throw new UnsupportedAlgorithmException(
                sprintf(
                    'rsa-oaep digest/MGF combination \'%s\'/\'%s\' is not supported.',
                    $digestAlg,
                    $mgf,
                ),
            ),
        };
    }
}
