<?php

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Backend\HMAC as HMAC_Backend;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Class implementing the HMAC signature algorithm
 *
 * @package SimpleSAML\XMLSecurity\Alg\Signature
 */
class HMAC extends AbstractSigner implements SignatureAlgorithm
{
    /** @var string */
    protected string $default_backend = HMAC_Backend::class;


    /**
     * HMAC constructor.
     *
     * @param \SimpleSAML\XMLSecurity\Key\SymmetricKey $key The symmetric key to use.
     * @param string $digest The identifier of the digest algorithm to use.
     */
    public function __construct(SymmetricKey $key, string $digest = Constants::DIGEST_SHA1)
    {
        parent::__construct($key, $digest);
    }
}
