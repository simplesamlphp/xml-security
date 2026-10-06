<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Backend\SignatureBackend;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\AgentSignatureMismatchException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

use function hash;
use function sprintf;

/**
 * Signature backend that delegates RSA signing to the Private Key Agent.
 *
 * sign() hashes the plaintext locally and forwards only the digest bytes to the agent;
 * private keys never enter this process. The signature the agent returns is verified
 * locally before it is returned to the caller. verify() delegates to a local OpenSSL
 * backend using the public key extracted from the certificate.
 *
 * This backend carries per-operation state: setDigestAlg() configures the digest used by the next
 * sign() call. One instance therefore serves one operation at a time. The PrivateKeyAgentRSA
 * wrapper clones the instance it is given for exactly that reason; code using this backend
 * directly must not share a single instance between operations that use different digests.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentSignatureBackend implements SignatureBackend
{
    private readonly PrivateKeyAgentHttpClient $httpClient;

    /** PHP hash function name corresponding to the current digest algorithm URI. */
    private string $phpDigest;

    /** Agent algorithm identifier (e.g. 'rsa-pkcs1-v1_5-sha256'). */
    private string $agentAlgorithm;

    /** Local OpenSSL backend for verify(). */
    private OpenSSL $localBackend;


    /**
     * @param \Psr\Http\Client\ClientInterface            $httpClient       PSR-18 HTTP client.
     * @param \Psr\Http\Message\RequestFactoryInterface   $requestFactory   PSR-17 request factory.
     * @param \Psr\Http\Message\StreamFactoryInterface    $streamFactory    PSR-17 stream factory.
     * @param string                                      $agentBaseUrl     Base URL of the agent.
     * @param \SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\TokenProvider    $tokenProvider  Token supplier.
     * @param \SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\KeyNameResolver  $keyNameResolver  Key-name resolver.
     * @param bool                                        $allowInsecureHttp Allow plain http:// (localhost/sidecar).
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the agent URL is invalid.
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $agentBaseUrl,
        private readonly TokenProvider $tokenProvider,
        private readonly KeyNameResolver $keyNameResolver,
        bool $allowInsecureHttp = false,
    ) {
        $this->httpClient = new PrivateKeyAgentHttpClient(
            $httpClient,
            $requestFactory,
            $streamFactory,
            $agentBaseUrl,
            $allowInsecureHttp,
        );

        $this->localBackend = new OpenSSL();
        // Default to SHA-256 so the backend is safe to use even if setDigestAlg() is never called again.
        $this->setDigestAlg(C::DIGEST_SHA256);
    }


    /**
     * Give the copy its own local OpenSSL backend, so reconfiguring the clone's digest does not
     * reach back into the original. The HTTP client, token provider and key-name resolver are
     * stateless and stay shared, which keeps connection reuse intact.
     */
    public function __clone(): void
    {
        $this->localBackend = clone $this->localBackend;
    }


    /**
     * Set the digest algorithm to use for signing.
     *
     * Only SHA-1/256/384/512 are supported; SHA-224 and unknown digests throw
     * UnsupportedAlgorithmException.
     *
     * @param string $digest The XMLSec digest algorithm URI.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If unsupported.
     */
    public function setDigestAlg(string $digest): void
    {
        // Throws UnsupportedAlgorithmException if not in the agent map.
        $agentAlgorithm = AlgorithmMap::getSigningAlgorithm($digest);

        $this->phpDigest = C::$DIGEST_ALGORITHMS[$digest];
        $this->agentAlgorithm = $agentAlgorithm;

        $this->localBackend->setDigestAlg($digest);
    }


    /**
     * Sign the given plaintext by hashing locally and delegating the RSA operation to the agent.
     *
     * The signature returned by the agent is verified locally against the public key of $key before
     * it is returned, at the cost of one public-key operation. A signature made with any other key,
     * or over any other plaintext, is rejected rather than returned.
     *
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $key Must be an X509Certificate.
     * @param string $plaintext The canonicalized SignedInfo to sign.
     *
     * @return string Binary RSA signature.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $key is not an X509Certificate.
     * @throws \SimpleSAML\XMLSecurity\Exception\MissingTokenException If no bearer token is available.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException If the key name cannot be resolved.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentUnavailableException If the agent cannot be reached.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentSignatureMismatchException If the returned signature does
     *   not verify against the certificate's public key.
     */
    public function sign(
        #[\SensitiveParameter]
        KeyInterface $key,
        string $plaintext,
    ): string {
        if (!($key instanceof X509Certificate)) {
            throw new InvalidArgumentException(
                sprintf(
                    'PrivateKeyAgentSignatureBackend requires an X509Certificate, got %s.',
                    $key::class,
                ),
            );
        }

        $keyName = $this->keyNameResolver->resolve($key);
        $token = $this->tokenProvider->getToken($keyName);
        $hashBytes = hash($this->phpDigest, $plaintext, binary: true);

        $signature = $this->httpClient->sign($keyName, $token, $this->agentAlgorithm, $hashBytes);

        // Fail closed: never hand back a signature we cannot attribute to the certificate we were given.
        if (!$this->verify($key, $plaintext, $signature)) {
            throw new AgentSignatureMismatchException(
                'Agent returned a signature that does not verify against the certificate\'s public key.',
            );
        }

        return $signature;
    }


    /**
     * Verify a signature locally using the public key extracted from the certificate.
     *
     * The agent is never called for verification. If $key is an X509Certificate its public
     * key is unwrapped first; other KeyInterface types are passed through directly.
     *
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $key
     * @param string $plaintext
     * @param string $signature
     *
     * @return bool
     */
    public function verify(
        #[\SensitiveParameter]
        KeyInterface $key,
        string $plaintext,
        string $signature,
    ): bool {
        $verifyKey = ($key instanceof X509Certificate) ? $key->getPublicKey() : $key;
        return $this->localBackend->verify($verifyKey, $plaintext, $signature);
    }
}
