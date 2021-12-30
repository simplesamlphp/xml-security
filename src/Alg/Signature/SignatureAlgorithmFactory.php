<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\AbstractKey;

use function in_array;

/**
 * Factory class to create and configure digital signature algorithms.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureAlgorithmFactory
{
    /**
     * An array holding the known classes extending \SimpleSAML\XMLSecurity\Alg\Signature\AbstractSigner.
     *
     * @var string[]
     */
    private static array $algorithms = [
        RSA::class,
        HMAC::class
    ];

    /**
     * An array of blacklisted algorithms.
     *
     * Defaults to RSA-SHA1 & HMAC-SHA1 due to the weakness of SHA1.
     *
     * @var string[]
     */
    private array $blacklist = [
        C::SIG_RSA_SHA1,
        C::SIG_HMAC_SHA1,
    ];

    /**
     * A cache of signers indexed by algorithm ID.
     *
     * @var string[]
     */
    private static array $cache = [];

    /**
     * Whether the factory has been initialized or not.
     *
     * @var bool
     */
    private static bool $initialized = false;


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

        // initialize the cache for supported algorithms per known signer
        if (!self::$initialized) {
            foreach (self::$algorithms as $algorithm) {
                self::updateCache($algorithm);
            }
            self::$initialized = true;
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
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If an error occurs, e.g. the given algorithm
     * is blacklisted, unknown or the given key is not suitable for it.
     */
    public function getAlgorithm(string $algId, AbstractKey $key): SignatureAlgorithm
    {
        Assert::true(
            !in_array($algId, $this->blacklist, true),
            sprintf('Blacklisted signature algorithm;  \'%s\'.', $algId),
            BlacklistedAlgorithmException::class
        );

        Assert::true(
            array_key_exists($algId, self::$cache),
            sprintf('Unsupported algorithm identifier;  \'%s\'.', $algId),
            UnsupportedAlgorithmException::class
        );

        return new self::$cache[$algId]($key, $algId);
    }


    /**
     * Register a signature algorithm for its use.
     *
     * @note Algorithms must extend \SimpleSAML\XMLSecurity\Alg\Signature\AbstractSigner.
     *
     * @param string $className
     */
    public static function registerAlgorithm(string $className): void
    {
        Assert::subclassOf(
            $className,
            AbstractSigner::class,
            'Cannot register algorithm "' . $className . '", must implement '
            . "\SimpleSAML\XMLSecurity\Alg\SignatureInterface.",
            InvalidArgumentException::class
        );

        self::$algorithms[] = $className;
        self::updateCache($className);
    }


    /**
     * Update the cache with a new signer implementation.
     *
     * @param string $signer
     */
    private static function updateCache(string $signer): void
    {
        /** @var \SimpleSAML\XMLSecurity\Alg\Signature\AbstractSigner $signer */
        foreach ($signer::getSupportedAlgorithms() as $algId) {
            self::$cache[$algId] = $signer;
        }
    }
}
