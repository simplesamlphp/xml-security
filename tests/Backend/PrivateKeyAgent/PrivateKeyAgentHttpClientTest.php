<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentHttpClient;
use SimpleSAML\XMLSecurity\Exception\AgentProtocolException;
use SimpleSAML\XMLSecurity\Exception\AgentUnavailableException;
use SimpleSAML\XMLSecurity\Exception\AuthenticationException;
use SimpleSAML\XMLSecurity\Exception\AuthorizationException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\InvalidRequestException;
use SimpleSAML\XMLSecurity\Exception\UnknownKeyException;

use function base64_encode;
use function json_encode;

/**
 * Tests for PrivateKeyAgentHttpClient.
 *
 * Uses pure PHPUnit mocks of PSR-18/17 interfaces, no real HTTP calls.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentHttpClientTest extends TestCase
{
    private const string TOKEN = 'super-secret-bearer-token';

    private const string KEY_NAME = 'my-key';

    private const string BASE_URL = 'https://agent.example.com';


    private ClientInterface&Stub $httpClient;

    private RequestFactoryInterface&Stub $requestFactory;

    private StreamFactoryInterface&Stub $streamFactory;

    private RequestInterface&Stub $request;


    protected function setUp(): void
    {
        $this->httpClient     = $this->createStub(ClientInterface::class);
        $this->requestFactory = $this->createStub(RequestFactoryInterface::class);
        $this->streamFactory  = $this->createStub(StreamFactoryInterface::class);
        $this->request        = $this->createStub(RequestInterface::class);

        // Default request chain stubs
        $this->request->method('withHeader')->willReturn($this->request);
        $this->request->method('withBody')->willReturn($this->request);
        $this->requestFactory->method('createRequest')->willReturn($this->request);
        $this->streamFactory->method('createStream')->willReturn($this->createStub(StreamInterface::class));
    }


    private function makeClient(
        bool $allowInsecureHttp = false,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
    ): PrivateKeyAgentHttpClient {
        return new PrivateKeyAgentHttpClient(
            $httpClient ?? $this->httpClient,
            $requestFactory ?? $this->requestFactory,
            $this->streamFactory,
            self::BASE_URL,
            $allowInsecureHttp,
        );
    }


    private function mockResponse(int $status, string $body): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);
        $response->method('getBody')->willReturn($stream);
        return $response;
    }


    public function testHttpUrlWithoutOptInThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PrivateKeyAgentHttpClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            'http://insecure.example.com',
        );
    }


    public function testHttpUrlWithOptInIsAccepted(): void
    {
        $rawSignature = 'opt-in-signature';
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['signature' => base64_encode($rawSignature)])),
        );

        $client = new PrivateKeyAgentHttpClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            'http://localhost:8080',
            allowInsecureHttp: true,
        );

        $result = $client->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
        $this->assertSame($rawSignature, $result);
    }


    public function testUnsupportedSchemeThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PrivateKeyAgentHttpClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            'ftp://agent.example.com',
        );
    }


    public function testInsecureHttpToRemoteHostIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeInsecureClient('http://agent.example.com');
    }


    public function testInsecureHttpToLoopbackIpIsAccepted(): void
    {
        $this->assertBaseUrlIsUsable('http://127.0.0.1:8080');
    }


    public function testInsecureHttpToIpv6LoopbackIsAccepted(): void
    {
        $this->assertBaseUrlIsUsable('http://[::1]:8080');
    }


    public function testInsecureHttpToLoopbackLikeHostnameIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeInsecureClient('http://localhost.evil.example');
    }


    public function testInsecureHttpWithUserinfoDisguisingHostIsRejected(): void
    {
        // The host component is "evil.example"; only a prefix check would be fooled by this.
        $this->expectException(InvalidArgumentException::class);
        $this->makeInsecureClient('http://localhost@evil.example');
    }


    public function testHttpsToRemoteHostIsUnaffectedByTheLoopbackRule(): void
    {
        $this->assertBaseUrlIsUsable('https://agent.example.com');
    }


    private function makeInsecureClient(string $baseUrl): PrivateKeyAgentHttpClient
    {
        return new PrivateKeyAgentHttpClient(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $baseUrl,
            allowInsecureHttp: true,
        );
    }


    /**
     * Assert that the base URL is accepted at construction and that the resulting client works.
     */
    private function assertBaseUrlIsUsable(string $baseUrl): void
    {
        $rawSignature = 'accepted-signature';
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['signature' => base64_encode($rawSignature)])),
        );

        $result = $this->makeInsecureClient($baseUrl)->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');

        $this->assertSame($rawSignature, $result);
    }


    public function testInvalidKeyNameThrowsBeforeHttpCall(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $client = $this->makeClient(httpClient: $httpClient);
        $this->expectException(InvalidArgumentException::class);
        $client->sign('../escape', self::TOKEN, 'rsa-pkcs1-v1_5-sha256', 'hashbytes');
    }


    public function testEmptyKeyNameThrows(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $client = $this->makeClient(httpClient: $httpClient);
        $this->expectException(InvalidArgumentException::class);
        $client->decrypt('', self::TOKEN, 'rsa-pkcs1-oaep-mgf1-sha256', 'data');
    }


    public function testTooLongKeyNameThrows(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $client = $this->makeClient(httpClient: $httpClient);
        $this->expectException(InvalidArgumentException::class);
        $client->sign(str_repeat('x', 65), self::TOKEN, 'rsa-pkcs1-v1_5-sha256', 'hashbytes');
    }


    public function testSignHappyPath(): void
    {
        $rawSignature = 'binary-signature-bytes';
        $response = $this->mockResponse(200, json_encode([
            'signature' => base64_encode($rawSignature),
        ]));
        $this->httpClient->method('sendRequest')->willReturn($response);

        $client = $this->makeClient();
        $result = $client->sign(self::KEY_NAME, self::TOKEN, 'rsa-pkcs1-v1_5-sha256', 'hashbytes');

        $this->assertSame($rawSignature, $result);
    }


    public function testSignRequestUsesCorrectUrlAndMethod(): void
    {
        $rawSignature = 'sig';
        $response = $this->mockResponse(200, json_encode([
            'signature' => base64_encode($rawSignature),
        ]));
        $this->httpClient->method('sendRequest')->willReturn($response);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with('POST', self::BASE_URL . '/v1/sign/' . self::KEY_NAME)
            ->willReturn($this->request);

        $this->makeClient(requestFactory: $requestFactory)->sign(
            self::KEY_NAME,
            self::TOKEN,
            'rsa-pkcs1-v1_5-sha256',
            'hashbytes',
        );
    }


    public function testSignRequestHasBearerToken(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('withBody')->willReturn($request);
        $requestFactory = $this->createStub(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['signature' => base64_encode('sig')])),
        );

        $request
            ->expects($this->atLeastOnce())
            ->method('withHeader')
            ->willReturnCallback(function (string $name, string $value) use ($request) {
                if ($name === 'Authorization') {
                    $this->assertSame('Bearer ' . self::TOKEN, $value);
                }
                return $request;
            });

        $this->makeClient(requestFactory: $requestFactory)->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testDecryptHappyPath(): void
    {
        $plaintext = 'decrypted-session-key';
        $response = $this->mockResponse(200, json_encode([
            'decrypted_data' => base64_encode($plaintext),
        ]));
        $this->httpClient->method('sendRequest')->willReturn($response);

        $client = $this->makeClient();
        $result = $client->decrypt(self::KEY_NAME, self::TOKEN, 'rsa-pkcs1-oaep-mgf1-sha256', 'ciphertext');

        $this->assertSame($plaintext, $result);
    }


    public function testDecryptRequestUsesCorrectUrl(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['decrypted_data' => base64_encode('plain')])),
        );

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->with('POST', self::BASE_URL . '/v1/decrypt/' . self::KEY_NAME)
            ->willReturn($this->request);

        $this->makeClient(requestFactory: $requestFactory)->decrypt(
            self::KEY_NAME,
            self::TOKEN,
            'algo',
            'cipher',
        );
    }


    public function testHttp400ThrowsInvalidRequestException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(400, '{}'));
        $this->expectException(InvalidRequestException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp401ThrowsAuthenticationException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(401, '{}'));
        $this->expectException(AuthenticationException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp403ThrowsAuthorizationException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(403, '{}'));
        $this->expectException(AuthorizationException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp404ThrowsUnknownKeyException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(404, '{}'));
        $this->expectException(UnknownKeyException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp429ThrowsAgentUnavailableException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(429, '{}'));
        $this->expectException(AgentUnavailableException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp500ThrowsAgentUnavailableException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(500, '{}'));
        $this->expectException(AgentUnavailableException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testHttp503ThrowsAgentUnavailableException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(503, '{}'));
        $this->expectException(AgentUnavailableException::class);
        $this->makeClient()->decrypt(self::KEY_NAME, self::TOKEN, 'algo', 'cipher');
    }


    public function testHttp405ThrowsAgentProtocolException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(405, '{}'));
        $this->expectException(AgentProtocolException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testNetworkErrorThrowsAgentUnavailableException(): void
    {
        $networkError = new class ('Connection refused') extends \RuntimeException implements ClientExceptionInterface {
        };
        $this->httpClient->method('sendRequest')->willThrowException($networkError);
        $this->expectException(AgentUnavailableException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testNetworkErrorDoesNotLeakTokenOrChainException(): void
    {
        $networkError = new class (
            'Connection refused while sending Authorization: Bearer ' . self::TOKEN,
        ) extends \RuntimeException implements ClientExceptionInterface {
        };
        $this->httpClient->method('sendRequest')->willThrowException($networkError);

        try {
            $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
            $this->fail('Expected AgentUnavailableException was not thrown.');
        } catch (AgentUnavailableException $e) {
            $this->assertSame('Agent is unreachable.', $e->getMessage());
            $this->assertNull($e->getPrevious());
        }
    }


    public function testMissingSignatureFieldThrowsAgentProtocolException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['other' => 'field'])),
        );
        $this->expectException(AgentProtocolException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testInvalidBase64InSignatureThrowsAgentProtocolException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['signature' => '!!!not-base64!!!'])),
        );
        $this->expectException(AgentProtocolException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testMissingDecryptedDataFieldThrowsAgentProtocolException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, json_encode(['signature' => base64_encode('wrong')])),
        );
        $this->expectException(AgentProtocolException::class);
        $this->makeClient()->decrypt(self::KEY_NAME, self::TOKEN, 'algo', 'cipher');
    }


    public function testInvalidJsonResponseThrowsAgentProtocolException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->mockResponse(200, 'not-json'),
        );
        $this->expectException(AgentProtocolException::class);
        $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
    }


    public function testTokenNotInAuthenticationExceptionMessage(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(401, '{}'));

        try {
            $this->makeClient()->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
            $this->fail('Expected AuthenticationException was not thrown.');
        } catch (AuthenticationException $e) {
            $this->assertStringNotContainsString(self::TOKEN, $e->getMessage());
        }
    }


    public function testTokenNotInAuthorizationExceptionMessage(): void
    {
        $this->httpClient->method('sendRequest')->willReturn($this->mockResponse(403, '{}'));

        try {
            $this->makeClient()->decrypt(self::KEY_NAME, self::TOKEN, 'algo', 'cipher');
            $this->fail('Expected AuthorizationException was not thrown.');
        } catch (AuthorizationException $e) {
            $this->assertStringNotContainsString(self::TOKEN, $e->getMessage());
        }
    }


    public function testTokenWithNewlineThrowsBeforeHttpCall(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $token = "tok\n";

        try {
            $this->makeClient(httpClient: $httpClient)->sign(self::KEY_NAME, $token, 'algo', 'hash');
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringNotContainsString($token, $e->getMessage());
        }
    }


    public function testTokenWithSpaceThrowsBeforeHttpCall(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $token = 'tok with space';

        try {
            $this->makeClient(httpClient: $httpClient)->decrypt(self::KEY_NAME, $token, 'algo', 'cipher');
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringNotContainsString($token, $e->getMessage());
        }
    }


    public function testNonAsciiTokenThrowsBeforeHttpCall(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');
        $token = "tok\xC3\xA9";

        try {
            $this->makeClient(httpClient: $httpClient)->sign(self::KEY_NAME, $token, 'algo', 'hash');
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringNotContainsString($token, $e->getMessage());
        }
    }


    public function testValidTokenPassingRegexButRejectedByRequestFactoryDoesNotLeak(): void
    {
        // Simulate a third-party PSR-7 implementation rejecting a token that
        // nevertheless passes our own TOKEN_PATTERN validation, to prove the
        // defense-in-depth try/catch around the withHeader() chain works
        // independently of the regex.
        $request = $this->createStub(RequestInterface::class);
        $request->method('withBody')->willReturn($request);
        $request->method('withHeader')->willThrowException(
            new \InvalidArgumentException('Invalid header value: Bearer ' . self::TOKEN),
        );

        $requestFactory = $this->createStub(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        try {
            $this->makeClient(requestFactory: $requestFactory)->sign(self::KEY_NAME, self::TOKEN, 'algo', 'hash');
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringNotContainsString(self::TOKEN, $e->getMessage());
        }
    }
}
