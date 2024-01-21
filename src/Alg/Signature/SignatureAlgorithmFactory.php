<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Alg\AbstractAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;

/**
 * Factory class to create and configure digital signature algorithms.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureAlgorithmFactory extends AbstractAlgorithmFactory
{
    /**
     * An array of blacklisted algorithms.
     *
     * Defaults to RSA-SHA1 & HMAC-SHA1 due to the weakness of SHA1.
     *
     * @var string[]
     */
    private const DEFAULT_BLACKLIST = [
        C::SIG_RSA_SHA1,
        C::SIG_HMAC_SHA1,
    ];

    /**
     * A cache of algorithm implementations indexed by algorithm ID.
     *
     * @var array<string, \SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmInterface>
     */
    protected static array $cache = [];

    /**
     * Whether the factory has been initialized or not.
     *
     * @var bool
     */
    protected static bool $initialized = false;


    /**
     * Build a factory that creates signature algorithms.
     *
     * @param string[]|null $blacklist A list of algorithms forbidden for their use.
     */
    public function __construct(array $blacklist = null)
    {
        parent::__construct(
            $blacklist ?? self::DEFAULT_BLACKLIST,
            [
                RSA::class,
                HMAC::class,
            ],
        );
    }


    /**
     * Get the name of the abstract class our algorithm implementations must extend.
     *
     * @return string
     */
    protected static function getExpectedParent(): string
    {
        return SignatureAlgorithmInterface::class;
    }
}
