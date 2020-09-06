<?php

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;

/**
 * Class implementing the RSA signature algorithm.
 *
 * @package SimpleSAML\XMLSecurity\Alg\Signature
 */
class RSA extends AbstractSigner implements SignatureAlgorithm
{
    /** @var string */
    protected string $default_backend = OpenSSL::class;


    /**
     * RSA constructor.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AsymmetricKey $key The asymmetric key (either public or private) to use.
     * @param string $digest The identifier of the digest algorithm to use.
     */
    public function __construct(AsymmetricKey $key, string $digest = Constants::DIGEST_SHA1)
    {
        parent::__construct($key, $digest);
    }
}
