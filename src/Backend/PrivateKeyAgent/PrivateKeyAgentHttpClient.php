<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use SimpleSAML\XMLSecurity\Exception\AgentProtocolException;
use SimpleSAML\XMLSecurity\Exception\AgentUnavailableException;
use SimpleSAML\XMLSecurity\Exception\AuthenticationException;
use SimpleSAML\XMLSecurity\Exception\AuthorizationException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\InvalidRequestException;
use SimpleSAML\XMLSecurity\Exception\UnknownKeyException;

use function base64_decode;
use function base64_encode;
use function filter_var;
use function is_string;
use function json_decode;
use function json_encode;
use function parse_url;
use function preg_match;
use function rawurlencode;
use function rtrim;
use function str_starts_with;
use function strtolower;
use function trim;

/**
 * Internal HTTP transport helper for communicating with the Private Key Agent.
 *
 * This class is not part of the public API; it is an implementation detail of
 * PrivateKeyAgentSignatureBackend and PrivateKeyAgentEncryptionBackend.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentHttpClient
{
    private const string KEY_NAME_PATTERN = '/^[a-zA-Z0-9_-]{1,64}$/';

    private const string TOKEN_PATTERN = '/^[A-Za-z0-9\-._~+\/]+=*$/D';


    private readonly string $baseUrl;


    /**
     * @param \Psr\Http\Client\ClientInterface            $httpClient      PSR-18 HTTP client.
     * @param \Psr\Http\Message\RequestFactoryInterface   $requestFactory  PSR-17 request factory.
     * @param \Psr\Http\Message\StreamFactoryInterface    $streamFactory   PSR-17 stream factory.
     * @param string                                      $agentBaseUrl    Base URL of the agent (must be https://).
     * @param bool                                        $allowInsecureHttp Allow plain http:// (loopback only).
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the URL scheme is not allowed.
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        string $agentBaseUrl,
        bool $allowInsecureHttp = false,
    ) {
        $isHttps = str_starts_with($agentBaseUrl, 'https://');
        $isHttp = str_starts_with($agentBaseUrl, 'http://');

        if (!$isHttps && !($allowInsecureHttp && $isHttp)) {
            throw new InvalidArgumentException(
                'Agent base URL must use https://; use allowInsecureHttp=true for http:// (e.g. localhost).',
            );
        }

        // Plain http:// is only tolerable when it cannot leave the host: the bearer token and every
        // ciphertext travel in cleartext, so a remote http:// agent is never an acceptable configuration.
        if ($isHttp && !self::isLoopbackUrl($agentBaseUrl)) {
            throw new InvalidArgumentException(
                'allowInsecureHttp only permits loopback hosts (localhost, 127.0.0.0/8, ::1).',
            );
        }

        $this->baseUrl = rtrim($agentBaseUrl, '/');
    }


    /**
     * Determine whether the host component of a URL is a loopback address.
     *
     * Uses the parsed host rather than a string prefix, so credentials cannot be used to disguise a
     * remote host (e.g. http://localhost@evil.example resolves to host "evil.example").
     */
    private static function isLoopbackUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return false;
        }

        // Strip the brackets that surround an IPv6 literal in a URL.
        $host = strtolower(trim($host, '[]'));

        if ($host === 'localhost' || $host === '::1') {
            return true;
        }

        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            && str_starts_with($host, '127.');
    }


    /**
     * Sign a pre-computed hash using the agent.
     *
     * @param string $keyName   The agent key name (validated against [a-zA-Z0-9_-]{1,64}).
     * @param string $token     Bearer token for authentication.
     * @param string $algorithm Agent algorithm identifier (e.g. 'rsa-pkcs1-v1_5-sha256').
     * @param string $hashBytes Raw binary hash bytes.
     *
     * @return string Raw binary signature.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the key name is invalid.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentUnavailableException On network errors.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthenticationException On HTTP 401.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthorizationException On HTTP 403.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException On HTTP 404.
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidRequestException On HTTP 400.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentProtocolException On unexpected response.
     */
    public function sign(
        string $keyName,
        #[\SensitiveParameter] string $token,
        string $algorithm,
        string $hashBytes,
    ): string {
        $this->validateKeyName($keyName);
        $url = $this->baseUrl . '/v1/sign/' . rawurlencode($keyName);
        $body = json_encode([
            'algorithm' => $algorithm,
            'hash'      => base64_encode($hashBytes),
        ]);

        $response = $this->sendRequest($url, $token, $body);
        $data = $this->decodeJsonResponse($response);

        if (!isset($data['signature']) || !is_string($data['signature'])) {
            throw new AgentProtocolException('Agent sign response is missing the "signature" field.');
        }

        $decoded = base64_decode($data['signature'], strict: true);
        if ($decoded === false) {
            throw new AgentProtocolException('Agent sign response contains invalid base64 in "signature".');
        }

        return $decoded;
    }


    /**
     * Decrypt RSA-encrypted data using the agent.
     *
     * @param string $keyName   The agent key name (validated against [a-zA-Z0-9_-]{1,64}).
     * @param string $token     Bearer token for authentication.
     * @param string $algorithm Agent algorithm identifier (e.g. 'rsa-pkcs1-oaep-mgf1-sha256').
     * @param string $ciphertext Raw binary ciphertext.
     *
     * @return string Raw binary plaintext.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the key name is invalid.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentUnavailableException On network errors.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthenticationException On HTTP 401.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthorizationException On HTTP 403.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException On HTTP 404.
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidRequestException On HTTP 400.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentProtocolException On unexpected response.
     */
    public function decrypt(
        string $keyName,
        #[\SensitiveParameter] string $token,
        string $algorithm,
        string $ciphertext,
    ): string {
        $this->validateKeyName($keyName);
        $url = $this->baseUrl . '/v1/decrypt/' . rawurlencode($keyName);
        $body = json_encode([
            'algorithm'      => $algorithm,
            'encrypted_data' => base64_encode($ciphertext),
        ]);

        $response = $this->sendRequest($url, $token, $body);
        $data = $this->decodeJsonResponse($response);

        if (!isset($data['decrypted_data']) || !is_string($data['decrypted_data'])) {
            throw new AgentProtocolException('Agent decrypt response is missing the "decrypted_data" field.');
        }

        $decoded = base64_decode($data['decrypted_data'], strict: true);
        if ($decoded === false) {
            throw new AgentProtocolException('Agent decrypt response contains invalid base64 in "decrypted_data".');
        }

        return $decoded;
    }


    /**
     * Validate a key name against the agent format.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If invalid.
     */
    private function validateKeyName(string $keyName): void
    {
        if (preg_match(self::KEY_NAME_PATTERN, $keyName) !== 1) {
            throw new InvalidArgumentException(
                'Invalid key name: must match [a-zA-Z0-9_-]{1,64}.',
            );
        }
    }


    /**
     * Validate a bearer token against the RFC 6750 b64token grammar.
     *
     * Rejects tokens before they ever reach ->withHeader(), so a malformed token
     * (e.g. a trailing newline from a secret file) never triggers a PSR-7
     * \InvalidArgumentException whose message would interpolate the token value.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If invalid.
     */
    private function validateToken(#[\SensitiveParameter] string $token): void
    {
        if (preg_match(self::TOKEN_PATTERN, $token) !== 1) {
            throw new InvalidArgumentException('Invalid bearer token supplied by TokenProvider.');
        }
    }


    /**
     * Build and send a POST request to the agent, returning the raw response body.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentUnavailableException On network errors or 429/5xx.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthenticationException On HTTP 401.
     * @throws \SimpleSAML\XMLSecurity\Exception\AuthorizationException On HTTP 403.
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException On HTTP 404.
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidRequestException On HTTP 400.
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the token is malformed.
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentProtocolException On unexpected status or response.
     */
    private function sendRequest(string $url, #[\SensitiveParameter] string $token, string $body): string
    {
        $this->validateToken($token);

        $stream = $this->streamFactory->createStream($body);

        try {
            $request = $this->requestFactory
                ->createRequest('POST', $url)
                ->withHeader('Authorization', 'Bearer ' . $token)
                ->withHeader('Content-Type', 'application/json')
                ->withBody($stream);
        } catch (\InvalidArgumentException) {
            throw new InvalidArgumentException('Invalid bearer token supplied by TokenProvider.');
        }

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface) {
            // Do not chain or quote the client exception: its message may embed the Authorization header.
            throw new AgentUnavailableException('Agent is unreachable.');
        }

        $statusCode = $response->getStatusCode();
        $responseBody = (string) $response->getBody();

        return match (true) {
            $statusCode === 200              => $responseBody,
            $statusCode === 400              => throw new InvalidRequestException(
                'Agent rejected the request (HTTP 400).',
            ),
            $statusCode === 401              => throw new AuthenticationException(
                'Agent authentication failed (HTTP 401).',
            ),
            $statusCode === 403              => throw new AuthorizationException(
                'Agent authorisation denied (HTTP 403).',
            ),
            $statusCode === 404              => throw new UnknownKeyException(
                'Agent key not found (HTTP 404).',
            ),
            $statusCode === 429 || $statusCode >= 500 => throw new AgentUnavailableException(
                sprintf('Agent temporarily unavailable (HTTP %d).', $statusCode),
            ),
            default                          => throw new AgentProtocolException(
                sprintf('Unexpected HTTP status code from agent: %d.', $statusCode),
            ),
        };
    }


    /**
     * Decode and validate a JSON response body.
     *
     * @return array<string, mixed>
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\AgentProtocolException On invalid JSON.
     */
    private function decodeJsonResponse(string $body): array
    {
        $data = json_decode($body, associative: true);
        if (!is_array($data)) {
            throw new AgentProtocolException('Agent returned invalid or non-object JSON response.');
        }

        return $data;
    }
}
