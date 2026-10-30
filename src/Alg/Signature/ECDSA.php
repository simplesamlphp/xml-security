<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;

/**
 * Class implementing the ECDSA signature algorithm.
 *
 * @package simplesamlphp/xml-security
 */
final class ECDSA extends AbstractSigner implements SignatureAlgorithmInterface
{
    /**
     * RSA constructor.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AsymmetricKey $key The asymmetric key (either public or private) to use.
     * @param string $algId The identifier of this algorithm.
     */
    public function __construct(
        #[\SensitiveParameter]
        AsymmetricKey $key,
        string $algId,
    ) {
        parent::__construct($key, $algId, C::$ECDSA_DIGESTS[$algId]);
    }


    /**
     * @return string[]
     */
    public static function getSupportedAlgorithms(): array
    {
        return array_keys(C::$ECDSA_DIGESTS);
    }
}
