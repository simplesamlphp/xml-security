<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Encryption;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Alg\AbstractAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;

/**
 * Factory class to create and configure encryption algorithms.
 *
 * @package simplesamlphp/xml-security
 */
final class EncryptionAlgorithmFactory extends AbstractAlgorithmFactory
{
    /**
     * An array of blacklisted algorithms.
     *
     * Defaults to 3DES.
     *
     * @var string[]
     */
    private const DEFAULT_BLACKLIST = [
        C::BLOCK_ENC_3DES,
    ];

    /**
     * A cache of algorithm implementations indexed by algorithm ID.
     *
     * @var array<string, \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface>
     */
    protected static array $cache = [];

    /**
     * Whether the factory has been initialized or not.
     *
     * @var bool
     */
    protected static bool $initialized = false;


    /**
     * Build a factory that creates encryption algorithms.
     *
     * @param string[]|null $blacklist A list of algorithms forbidden for their use.
     */
    public function __construct(array $blacklist = null)
    {
        parent::__construct(
            $blacklist ?? self::DEFAULT_BLACKLIST,
            [
                TripleDES::class,
                AES::class,
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
        return EncryptionAlgorithmInterface::class;
    }
}
