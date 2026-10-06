<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

/**
 * A key-name resolver that returns one fixed key name for any certificate.
 *
 * The name is validated at construction time; invalid names fail fast at boot.
 *
 * WARNING: this resolver performs no certificate-to-key binding. It ignores the certificate it is
 * given and returns its fixed key name for every input, so any certificate reaching the backend is
 * signed or decrypted with the configured key. The caller is therefore solely responsible for
 * guaranteeing that the certificate originates from local configuration or from already-validated
 * metadata, and never from the message being processed. Use this resolver only where that
 * certificate is a fixed local constant (test harnesses, single-key sidecars); prefer
 * {@see FingerprintKeyNameResolver}, which fails closed on an unexpected certificate, also when
 * there is only one key.
 *
 * @package simplesamlphp/xml-security
 */
final class StaticKeyNameResolver implements KeyNameResolver
{
    /**
     * @param string $keyName The key name to return for every certificate.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the key name is invalid.
     */
    public function __construct(
        private readonly string $keyName,
    ) {
        if (preg_match(self::KEY_NAME_PATTERN, $keyName) !== 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid key name \'%s\'; must match [a-zA-Z0-9_-]{1,64}.',
                    $keyName,
                ),
            );
        }
    }


    public function resolve(X509Certificate $certificate): string
    {
        return $this->keyName;
    }
}
