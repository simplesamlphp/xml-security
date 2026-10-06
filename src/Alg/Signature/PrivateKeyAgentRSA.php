<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Backend\SignatureBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

use function array_keys;
use function in_array;
use function sprintf;

/**
 * RSA signature wrapper that routes signing to the Private Key Agent for X509Certificate keys,
 * and uses local OpenSSL for PrivateKey/PublicKey (AsymmetricKey) instances.
 *
 * @package simplesamlphp/xml-security
 */
class PrivateKeyAgentRSA extends AbstractSigner implements SignatureAlgorithmInterface
{
    /**
     * Algorithm URIs the PKA backend can actually perform (PKCS#1 v1.5 only).
     *
     * @var string[]
     */
    private const array PKA_SUPPORTED_ALGORITHMS = [
        C::SIG_RSA_SHA1,
        C::SIG_RSA_SHA224,
        C::SIG_RSA_SHA256,
        C::SIG_RSA_SHA384,
        C::SIG_RSA_SHA512,
    ];


    /**
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface        $key        The key to use. Must be X509Certificate
     *   (routes to agent) or AsymmetricKey (uses local OpenSSL).
     * @param string                                           $algId      RSA signature algorithm URI.
     * @param \SimpleSAML\XMLSecurity\Backend\SignatureBackend $pkaBackend PKA backend used when $key is a certificate.
     *   Treated as a prototype: this instance takes its own copy, so a single backend registered once at boot
     *   can serve any number of concurrent algorithm instances.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $key is neither X509Certificate nor
     *   AsymmetricKey.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If $key is an X509Certificate and
     *   $algId is not one of the PKCS#1 v1.5 RSA signature algorithms supported by the PKA backend.
     */
    public function __construct(
        #[\SensitiveParameter]
        KeyInterface $key,
        string $algId,
        SignatureBackend $pkaBackend,
    ) {
        parent::__construct($key, $algId, C::$RSA_DIGESTS[$algId]);

        if ($key instanceof X509Certificate) {
            // Fail closed: the PKA backend only implements PKCS#1 v1.5 signing. Any other
            // algorithm (e.g. RSA-PSS) would otherwise be silently downgraded to v1.5 while
            // the XML signature method still declares the stronger scheme.
            if (!in_array($algId, self::PKA_SUPPORTED_ALGORITHMS, true)) {
                throw new UnsupportedAlgorithmException(
                    sprintf(
                        'Algorithm \'%s\' is not supported when routing to the Private Key Agent; '
                        . 'only PKCS#1 v1.5 RSA signature algorithms are supported for X509Certificate keys.',
                        $algId,
                    ),
                );
            }

            // Route to the PKA backend; local OpenSSL constructed by parent is replaced.
            // Clone it: the backend carries per-operation state (the digest set by setDigestAlg()),
            // while the registered instance is shared by every algorithm built from that closure.
            // Without a private copy, the last algorithm constructed would dictate the digest for
            // every signature still pending -- signatures are produced at toXML() time, not at sign().
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
        return array_keys(C::$RSA_DIGESTS);
    }
}
