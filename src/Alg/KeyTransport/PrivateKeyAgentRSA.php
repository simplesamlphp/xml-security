<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\KeyTransport;

use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

use function sprintf;

/**
 * RSA key-transport wrapper that routes decryption to the Private Key Agent for X509Certificate keys,
 * and uses local OpenSSL for PrivateKey/PublicKey (AsymmetricKey) instances. The choice follows
 * deterministically from the key type; an agent error never results in a local private-key operation.
 *
 * Inherits setOAEPParams() from AbstractKeyTransporter; OAEP parameters set before or after
 * construction are forwarded to whichever backend is active.
 *
 * @package simplesamlphp/xml-security
 */
class PrivateKeyAgentRSA extends AbstractKeyTransporter
{
    /**
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface          $key        The key to use. Must be X509Certificate
     *   (routes to agent) or AsymmetricKey (uses local OpenSSL).
     * @param string                                             $algId      RSA key-transport algorithm URI.
     * @param \SimpleSAML\XMLSecurity\Backend\EncryptionBackend $pkaBackend PKA backend used when $key is a certificate.
     *   Treated as a prototype: this instance takes its own copy, so a single backend registered once at boot
     *   can serve any number of concurrent algorithm instances.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $key is neither X509Certificate nor
     *   AsymmetricKey.
     */
    public function __construct(
        #[\SensitiveParameter]
        KeyInterface $key,
        string $algId,
        EncryptionBackend $pkaBackend,
    ) {
        parent::__construct($key, $algId);

        if ($key instanceof X509Certificate) {
            // Route to the PKA backend; local OpenSSL constructed by parent is replaced.
            // Clone it: the backend carries per-operation state (the cipher set by setCipher() and the
            // OAEP parameters read from the XML), while the registered instance is shared by every
            // algorithm built from that closure. Without a private copy, the last algorithm constructed
            // would dictate the cipher and OAEP parameters for every other one.
            $this->setBackend(clone $pkaBackend);
        } elseif (!($key instanceof AsymmetricKey)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s requires an X509Certificate or AsymmetricKey, got %s.',
                    self::class,
                    $key::class,
                ),
            );
        }
        // AsymmetricKey: keep the local OpenSSL default backend the parent constructed.
    }


    /**
     * @return string[]
     */
    public static function getSupportedAlgorithms(): array
    {
        return C::$KEY_TRANSPORT_ALGORITHMS;
    }
}
