<?php

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Key\AbstractKey;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;

/**
 * Factory class to create and configure digital signature algorithms.
 *
 * @package SimpleSAML\XMLSecurity\Alg\Signature
 */
class SignatureAlgorithmFactory
{
    /**
     * An array of blacklisted algorithms.
     *
     * Defaults to RSA-SHA1 & HMAC-SHA1 due to the weakness of SHA1.
     *
     * @var string[]
     */
    protected array $blacklist = [
        Constants::SIG_RSA_SHA1,
        Constants::SIG_HMAC_SHA1,
    ];


    /**
     * Build a factory that creates signature algorithms.
     *
     * @param string[]|null $blacklist
     */
    public function __construct(array $blacklist = null)
    {
        if ($blacklist !== null) {
            $this->blacklist = $blacklist;
        }
    }


    /**
     * Get a new object implementing the given digital signature algorithm.
     *
     * @param string $algId The identifier of the algorithm desired.
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The key to use with the given algorithm.
     *
     * @return \SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm An object implementing the given algorithm.
     *
     * @throws InvalidArgumentException If an error occurs, e.g. the given algorithm is blacklisted, unknown or the
     * given key is not suitable for it.
     */
    public function getAlgorithm(string $algId, AbstractKey $key): SignatureAlgorithm
    {
        if (in_array($algId, $this->blacklist)) {
            throw new InvalidArgumentException('Blacklisted signature algorithm');
        }

        // determine digest
        switch ($algId) {
            case Constants::SIG_RSA_SHA1:
            case Constants::SIG_HMAC_SHA1:
                $digest = Constants::DIGEST_SHA1;
                break;
            case Constants::SIG_RSA_SHA224:
            case Constants::SIG_HMAC_SHA224:
                $digest = Constants::DIGEST_SHA224;
                break;
            case Constants::SIG_RSA_SHA256:
            case Constants::SIG_HMAC_SHA256:
                $digest = Constants::DIGEST_SHA256;
                break;
            case Constants::SIG_RSA_SHA384:
            case Constants::SIG_HMAC_SHA384:
                $digest = Constants::DIGEST_SHA384;
                break;
            case Constants::SIG_RSA_SHA512:
            case Constants::SIG_HMAC_SHA512:
                $digest = Constants::DIGEST_SHA512;
                break;
            case Constants::SIG_RSA_RIPEMD160:
            case Constants::SIG_HMAC_RIPEMD160:
                $digest = Constants::DIGEST_RIPEMD160;
                break;
            default:
                throw new RuntimeException('Unsupported signature algorithm');
        }

        // create instance
        switch ($algId) {
            case Constants::SIG_RSA_SHA1:
            case Constants::SIG_RSA_SHA224:
            case Constants::SIG_RSA_SHA256:
            case Constants::SIG_RSA_SHA384:
            case Constants::SIG_RSA_SHA512:
            case Constants::SIG_RSA_RIPEMD160:
                if ($key instanceof AsymmetricKey) {
                    return new RSA($key, $digest);
                }
                break;
            case Constants::SIG_HMAC_SHA1:
            case Constants::SIG_HMAC_SHA224:
            case Constants::SIG_HMAC_SHA256:
            case Constants::SIG_HMAC_SHA384:
            case Constants::SIG_HMAC_SHA512:
            case Constants::SIG_HMAC_RIPEMD160:
                if ($key instanceof SymmetricKey) {
                    return new HMAC($key, $digest);
                }
                break;
        }
        throw new RuntimeException('Invalid type of key for algorithm');
    }
}
