<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;

/**
 * Class implementing the RSA signature algorithm.
 *
 * @package simplesamlphp/xml-security
 */
final class RSA extends AbstractSigner implements SignatureAlgorithm
{
    /** @var string */
    protected string $default_backend = OpenSSL::class;


    /**
     * RSA constructor.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AsymmetricKey $key The asymmetric key (either public or private) to use.
     * @param string $algId The identifier of this algorithm.
     */
    public function __construct(AsymmetricKey $key, string $algId = C::SIG_RSA_SHA256)
    {
        parent::__construct($key, $algId, C::$RSA_DIGESTS[$algId]);
    }


    /**
     * @inheritDoc
     */
    public static function getSupportedAlgorithms(): array
    {
        return array_keys(C::$RSA_DIGESTS);
    }
}
