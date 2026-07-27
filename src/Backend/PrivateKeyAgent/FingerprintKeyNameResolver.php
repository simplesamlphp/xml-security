<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnknownKeyException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

use function sprintf;
use function strtolower;

/**
 * A key-name resolver that maps SHA-256 certificate fingerprints to key names.
 *
 * The map and all key names are validated at construction time.
 *
 * @package simplesamlphp/xml-security
 */
final class FingerprintKeyNameResolver implements KeyNameResolver
{
    /** @var array<string, string> Fingerprint (hex SHA-256) → key name */
    private readonly array $map;


    /**
     * @param array<string, string> $map Map of hex SHA-256 fingerprint → key name.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException On malformed or duplicate (case-insensitive)
     *   fingerprints, or invalid key names.
     */
    public function __construct(array $map)
    {
        // Validate key names
        foreach ($map as $fingerprint => $keyName) {
            if (preg_match(self::KEY_NAME_PATTERN, $keyName) !== 1) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid key name \'%s\' for fingerprint \'%s\'; must match [a-zA-Z0-9_-]{1,64}.',
                        $keyName,
                        $fingerprint,
                    ),
                );
            }
        }

        // Validate fingerprint format and normalise to lowercase; reject fingerprints that collide only by case.
        $normalisedMap = [];
        foreach ($map as $fingerprint => $keyName) {
            if (preg_match('/^[0-9a-fA-F]{64}$/', (string) $fingerprint) !== 1) {
                throw new InvalidArgumentException(
                    sprintf('Invalid fingerprint \'%s\'; must be 64 hex characters.', $fingerprint),
                );
            }

            $normalisedFingerprint = strtolower((string) $fingerprint);
            if (isset($normalisedMap[$normalisedFingerprint])) {
                throw new InvalidArgumentException(
                    sprintf('Duplicate fingerprint \'%s\' in map (case-insensitive).', $normalisedFingerprint),
                );
            }

            $normalisedMap[$normalisedFingerprint] = $keyName;
        }

        $this->map = $normalisedMap;
    }


    public function resolve(X509Certificate $certificate): string
    {
        $fingerprint = strtolower($certificate->getRawThumbprint(C::DIGEST_SHA256));

        if (!isset($this->map[$fingerprint])) {
            throw new UnknownKeyException(
                sprintf('No key name found for certificate with SHA-256 fingerprint \'%s\'.', $fingerprint),
            );
        }

        return $this->map[$fingerprint];
    }
}
