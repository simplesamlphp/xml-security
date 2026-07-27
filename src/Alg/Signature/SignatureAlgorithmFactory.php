<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;

use function array_key_exists;
use function get_debug_type;
use function sprintf;

/**
 * Factory class to create and configure digital signature algorithms.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureAlgorithmFactory
{
    /**
     * An array of blacklisted algorithms.
     *
     * Defaults to RSA-SHA1, RSAPSS-SHA1 & HMAC-SHA1 due to the weakness of SHA1.
     *
     * @var string[]
     */
    public const array DEFAULT_BLACKLIST = [
        C::SIG_RSA_SHA1,
        C::SIG_RSA_PSS_SHA1,
        C::SIG_HMAC_SHA1,
    ];

    /**
     * An array of default algorithms that can be used.
     *
     * @var class-string[]
     */
    private const array SUPPORTED_DEFAULTS = [
        RSA::class,
        HMAC::class,
    ];


    /**
     * A cache of algorithm implementations indexed by algorithm ID.
     *
     * @var array<string, class-string<\SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmInterface>>
     */
    protected static array $cache = [];

    /**
     * Closure factories registered via registerAlgorithmFactory(), keyed by algorithm ID.
     *
     * @var array<string, \Closure>
     */
    protected static array $closureMap = [];

    /**
     * Whether the factory has been initialized or not.
     */
    protected static bool $initialized = false;


    /**
     * Build a factory that creates algorithms.
     *
     * @param string[] $blacklist A list of algorithms forbidden for their use.
     */
    public function __construct(
        protected array $blacklist = self::DEFAULT_BLACKLIST,
    ) {
        // initialize the cache for supported algorithms per known implementation
        if (!self::$initialized) {
            foreach (self::SUPPORTED_DEFAULTS as $algorithm) {
                foreach ($algorithm::getSupportedAlgorithms() as $algId) {
                    if (array_key_exists($algId, self::$cache) && !array_key_exists($algId, $this->blacklist)) {
                        /*
                         * If the key existed before initialization, that means someone registered a handler for this
                         * algorithm, so we should respect that and skip registering the default here.
                         */
                        continue;
                    }
                    self::$cache[$algId] = $algorithm;
                }
            }
            self::$initialized = true;
        }
    }


    /**
     * Get a new object implementing the given digital signature algorithm.
     *
     * @param string $algId The identifier of the algorithm desired.
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $key The key to use with the given algorithm.
     *
     * @return \SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmInterface An object implementing the given
     * algorithm.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If an error occurs, e.g. the given
     * algorithm is blacklisted, unknown or the given key is not suitable for it.
     */
    public function getAlgorithm(
        string $algId,
        #[\SensitiveParameter]
        KeyInterface $key,
    ): SignatureAlgorithmInterface {
        Assert::notInArray(
            $algId,
            $this->blacklist,
            sprintf('Blacklisted algorithm: \'%s\'.', $algId),
            BlacklistedAlgorithmException::class,
        );

        if (array_key_exists($algId, self::$closureMap)) {
            $instance = (self::$closureMap[$algId])($key, $algId);
            if (!($instance instanceof SignatureAlgorithmInterface)) {
                throw new RuntimeException(
                    sprintf(
                        'Registered factory for \'%s\' returned %s instead of %s.',
                        $algId,
                        get_debug_type($instance),
                        SignatureAlgorithmInterface::class,
                    ),
                );
            }
            return $instance;
        }

        Assert::keyExists(
            self::$cache,
            $algId,
            sprintf('Unknown or unsupported algorithm: \'%s\'.', $algId),
            UnsupportedAlgorithmException::class,
        );

        return new self::$cache[$algId]($key, $algId);
    }


    /**
     * Register an implementation of some algorithm(s) for its use.
     *
     * @param class-string $className
     */
    public static function registerAlgorithm(string $className): void
    {
        Assert::implementsInterface(
            $className,
            SignatureAlgorithmInterface::class,
            sprintf(
                'Cannot register algorithm "%s", must implement %s.',
                $className,
                SignatureAlgorithmInterface::class,
            ),
        );

        foreach ($className::getSupportedAlgorithms() as $algId) {
            self::$cache[$algId] = $className;
        }
    }


    /**
     * Register a closure factory for a specific algorithm ID.
     *
     * The closure is invoked by getAlgorithm() when the given algorithm is requested,
     * taking priority over any class-string registered via registerAlgorithm(). The
     * blacklist check still runs before the closure is invoked. The returned value must
     * implement SignatureAlgorithmInterface; a wrong return type throws RuntimeException.
     *
     * Lifetime: registration is process-global and is not scoped to a factory instance or to a
     * request. It lives until unregisterAlgorithmFactory()/clearAlgorithmFactories() is called or
     * the process ends, which matters under worker SAPIs (FrankenPHP, RoadRunner, Swoole) where the
     * process is reused across requests. Everything the closure captures -- backend, token provider,
     * key-name resolver -- is shared by every subsequent caller of that algorithm URI.
     *
     * @param string   $algId   The algorithm URI to register the factory for.
     * @param \Closure $factory Callable with signature (KeyInterface $key, string $algId): SignatureAlgorithmInterface.
     */
    public static function registerAlgorithmFactory(string $algId, \Closure $factory): void
    {
        self::$closureMap[$algId] = $factory;
    }


    /**
     * Remove the closure factory registered for a specific algorithm ID.
     *
     * Removing a factory that was never registered is a no-op. Any class-string registered via
     * registerAlgorithm() for the same algorithm remains in place and takes over again.
     *
     * @param string $algId The algorithm URI to unregister the factory for.
     */
    public static function unregisterAlgorithmFactory(string $algId): void
    {
        unset(self::$closureMap[$algId]);
    }


    /**
     * Remove all closure factories registered via registerAlgorithmFactory().
     *
     * Only the closure registrations are cleared; the built-in algorithm registry populated from
     * registerAlgorithm() is left untouched.
     */
    public static function clearAlgorithmFactories(): void
    {
        self::$closureMap = [];
    }
}
