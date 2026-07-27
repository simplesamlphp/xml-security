<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SensitiveParameter;
use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\Backend\OAEPParametersAware;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\Key\X509Certificate;

use function in_array;
use function sprintf;

/**
 * Encryption backend that delegates RSA decryption to the Private Key Agent.
 *
 * decrypt() sends only the RSA-wrapped key bytes to the agent; private keys never enter
 * this process. encrypt() delegates to a local OpenSSL backend using the public key
 * extracted from the certificate.
 *
 * This backend carries per-operation state: setCipher() and setOAEPParams() configure the
 * algorithm used by the next decrypt() call. One instance therefore serves one operation at a
 * time. The PrivateKeyAgentRSA wrapper clones the instance it is given for exactly that reason;
 * code using this backend directly must not share a single instance between operations that use
 * different ciphers or OAEP parameters.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentEncryptionBackend implements EncryptionBackend, OAEPParametersAware
{
    private readonly PrivateKeyAgentHttpClient $httpClient;

    /** Current key-transport cipher URI, or null if not yet set. */
    private ?string $cipherUri = null;

    /** Current OAEP digest algorithm URI, or null for algorithm default. */
    private ?string $oaepDigestAlg = null;

    /** Current MGF URI, or null for algorithm default. */
    private ?string $oaepMgf = null;

    /** Local OpenSSL backend for encrypt(). */
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
    }


    /**
     * Give the copy its own local OpenSSL backend, so reconfiguring the clone's cipher does not
     * reach back into the original. The HTTP client, token provider and key-name resolver are
     * stateless and stay shared, which keeps connection reuse intact.
     */
    public function __clone(): void
    {
        $this->localBackend = clone $this->localBackend;
    }


    /**
     * Set the key-transport cipher.
     *
     * Only RSA key-transport algorithm URIs are accepted; block ciphers are not supported
     * by this backend. Resets the stored OAEP parameters to null.
     *
     * @param string $cipher A key-transport algorithm URI from C::$KEY_TRANSPORT_ALGORITHMS.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the cipher is not a supported
     *   RSA key-transport algorithm.
     */
    public function setCipher(string $cipher): void
    {
        if (!in_array($cipher, C::$KEY_TRANSPORT_ALGORITHMS, strict: true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'PKA encryption backend only supports RSA key-transport ciphers; \'%s\' is not supported.',
                    $cipher,
                ),
            );
        }

        $this->cipherUri = $cipher;
        $this->oaepDigestAlg = null;
        $this->oaepMgf = null;

        $this->localBackend->setCipher($cipher);
    }


    /**
     * Configure OAEP digest/MGF parameters.
     *
     * Must be called after setCipher(); calling before setCipher() throws RuntimeException.
     * Validates the full cipher+digest+mgf combination via AlgorithmMap (fail-closed).
     *
     * @param string|null $digestAlg OAEP digest algorithm URI, or null for algorithm default.
     * @param string|null $mgf MGF URI, or null for algorithm default.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException If called before setCipher().
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If the combination is invalid.
     */
    public function setOAEPParams(?string $digestAlg = null, ?string $mgf = null): void
    {
        if ($this->cipherUri === null) {
            throw new RuntimeException('setOAEPParams() must be called after setCipher().');
        }

        // Validate the combination; throws UnsupportedAlgorithmException on invalid input.
        AlgorithmMap::getKeyTransportAlgorithm($this->cipherUri, $digestAlg, $mgf);

        $this->oaepDigestAlg = $digestAlg;
        $this->oaepMgf = $mgf;
    }


    /**
     * Encrypt a plaintext using the local OpenSSL backend with the public key extracted from
     * the certificate. The agent is never called for encryption.
     *
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $key The encryption key.
     * @param string $plaintext The plaintext to encrypt.
     *
     * @return string Ciphertext.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException
     */
    public function encrypt(
        #[SensitiveParameter]
        KeyInterface $key,
        string $plaintext,
    ): string {
        $this->localBackend->setOAEPParams($this->oaepDigestAlg, $this->oaepMgf);

        $encryptKey = ($key instanceof X509Certificate) ? $key->getPublicKey() : $key;
        return $this->localBackend->encrypt($encryptKey, $plaintext);
    }


    /**
     * Decrypt RSA-encrypted data by delegating the raw RSA operation to the agent.
     *
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $key Must be an X509Certificate.
     * @param string $ciphertext The RSA-encrypted bytes.
     *
     * @return string Plaintext.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $key is not an X509Certificate.
     * @throws \SimpleSAML\XMLSecurity\Exception\MissingTokenException If no bearer token is available.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException If the key name cannot be resolved.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentUnavailableException If the agent cannot be reached.
     */
    public function decrypt(
        #[SensitiveParameter]
        KeyInterface $key,
        string $ciphertext,
    ): string {
        if (!($key instanceof X509Certificate)) {
            throw new InvalidArgumentException(
                sprintf(
                    'PrivateKeyAgentEncryptionBackend requires an X509Certificate, got %s.',
                    $key::class,
                ),
            );
        }

        $agentAlgorithm = AlgorithmMap::getKeyTransportAlgorithm(
            $this->cipherUri ?? '',
            $this->oaepDigestAlg,
            $this->oaepMgf,
        );

        $keyName = $this->keyNameResolver->resolve($key);
        $token = $this->tokenProvider->getToken($keyName);

        return $this->httpClient->decrypt($keyName, $token, $agentAlgorithm, $ciphertext);
    }
}
