<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\KeyNameResolver;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentSignatureBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\TokenProvider;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\AgentSignatureMismatchException;
use SimpleSAML\XMLSecurity\Exception\AgentUnavailableException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function base64_encode;
use function hash;
use function json_encode;

/**
 * Tests for PrivateKeyAgentSignatureBackend.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentSignatureBackendTest extends TestCase
{
    private const string BASE_URL = 'https://agent.example.com';

    private const string KEY_NAME = 'my-signing-key';

    private const string TOKEN = 'secret-token';


    private static X509Certificate $certificate;

    private static PublicKey $publicKey;

    private ClientInterface&Stub $httpClient;

    private RequestFactoryInterface&Stub $requestFactory;

    private StreamFactoryInterface&Stub $streamFactory;

    private RequestInterface&Stub $request;

    private TokenProvider&Stub $tokenProvider;

    private KeyNameResolver&Stub $keyNameResolver;


    public static function setUpBeforeClass(): void
    {
        self::$certificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        self::$publicKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
    }


    protected function setUp(): void
    {
        // Use createStub() for all objects that only need willReturn(), no interaction assertions.
        $this->httpClient = $this->createStub(ClientInterface::class);
        $this->requestFactory = $this->createStub(RequestFactoryInterface::class);
        $this->streamFactory = $this->createStub(StreamFactoryInterface::class);
        $this->request = $this->createStub(RequestInterface::class);

        $this->request->method('withHeader')->willReturn($this->request);
        $this->request->method('withBody')->willReturn($this->request);
        $this->requestFactory->method('createRequest')->willReturn($this->request);
        $this->streamFactory->method('createStream')->willReturn($this->createStub(StreamInterface::class));

        $this->tokenProvider = $this->createStub(TokenProvider::class);
        $this->tokenProvider->method('getToken')->willReturn(self::TOKEN);

        $this->keyNameResolver = $this->createStub(KeyNameResolver::class);
        $this->keyNameResolver->method('resolve')->willReturn(self::KEY_NAME);
    }


    private function makeBackend(?ClientInterface $httpClient = null): PrivateKeyAgentSignatureBackend
    {
        return new PrivateKeyAgentSignatureBackend(
            $httpClient ?? $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            self::BASE_URL,
            $this->tokenProvider,
            $this->keyNameResolver,
        );
    }


    private function makeResponse(int $status, string $body): ResponseInterface
    {
        $stream = $this->createStub(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($status);
        $response->method('getBody')->willReturn($stream);
        return $response;
    }


    public function testSetDigestAlgAcceptsSupportedDigests(): void
    {
        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $backend->setDigestAlg(C::DIGEST_SHA384);
        $backend->setDigestAlg(C::DIGEST_SHA512);
        $backend->setDigestAlg(C::DIGEST_SHA1);
        $this->addToAssertionCount(1); // Verifies none of the above threw an exception.
    }


    public function testSetDigestAlgRejectsSha224(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $this->makeBackend()->setDigestAlg(C::DIGEST_SHA224);
    }


    public function testSetDigestAlgRejectsUnknownDigest(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $this->makeBackend()->setDigestAlg('http://example.com/unknown-hash');
    }


    /**
     * Produce the signature a correctly configured agent would return: the real
     * RSA-PKCS1v1.5 signature made with the private key matching self::$certificate.
     */
    private function realSignature(
        string $plaintext,
        string $digest = C::DIGEST_SHA256,
        string $keyFile = PEMCertificatesMock::PRIVATE_KEY,
    ): string {
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg($digest);

        return $localBackend->sign(PEMCertificatesMock::getPrivateKey($keyFile), $plaintext);
    }


    public function testSignSendsSha256HashToAgent(): void
    {
        $plaintext = 'some canonicalized SignedInfo data';
        $signature = $this->realSignature($plaintext);
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode($signature)])),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $result = $backend->sign(self::$certificate, $plaintext);

        $this->assertSame($signature, $result);
    }


    /**
     * Verify that sign() hashes locally and sends the exact expected hash length.
     */
    public function testSignSendsCorrectHashLength(): void
    {
        $plaintext = 'hello world';
        $expectedHash = hash('sha256', $plaintext, binary: true);
        $signature = $this->realSignature($plaintext);

        // Capture the stream body to verify the hash.
        $capturedBody = null;
        $this->streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );

        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode($signature)])),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $backend->sign(self::$certificate, $plaintext);

        $this->assertNotNull($capturedBody);
        $decoded = json_decode($capturedBody, true);
        $this->assertSame(base64_encode($expectedHash), $decoded['hash']);
        $this->assertSame('rsa-pkcs1-v1_5-sha256', $decoded['algorithm']);
    }


    /**
     * Verify the agent algorithm string for each supported digest.
     *
     * @dataProvider digestToAgentAlgorithmProvider
     */
    #[DataProvider('digestToAgentAlgorithmProvider')]
    public function testSignUsesCorrectAgentAlgorithm(string $digest, string $expectedAlgorithm): void
    {
        $capturedBody = null;
        $this->streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );

        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(
                200,
                json_encode(['signature' => base64_encode($this->realSignature('plaintext', $digest))]),
            ),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg($digest);
        $backend->sign(self::$certificate, 'plaintext');

        $decoded = json_decode($capturedBody, true);
        $this->assertSame($expectedAlgorithm, $decoded['algorithm']);
    }


    /** @return array<string, array{string, string}> */
    public static function digestToAgentAlgorithmProvider(): array
    {
        return [
            'sha1'   => [C::DIGEST_SHA1,   'rsa-pkcs1-v1_5-sha1'],
            'sha256' => [C::DIGEST_SHA256,  'rsa-pkcs1-v1_5-sha256'],
            'sha384' => [C::DIGEST_SHA384,  'rsa-pkcs1-v1_5-sha384'],
            'sha512' => [C::DIGEST_SHA512,  'rsa-pkcs1-v1_5-sha512'],
        ];
    }


    public function testSignRejectsNonCertificateKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $backend = $this->makeBackend();
        $backend->sign(self::$publicKey, 'plaintext');
    }


    public function testSignRejectsSymmetricKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $backend = $this->makeBackend();
        $backend->sign(SymmetricKey::generate(16), 'plaintext');
    }


    public function testSignPropagatesAgentException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(503, ''),
        );

        $this->expectException(AgentUnavailableException::class);
        $backend = $this->makeBackend();
        $backend->sign(self::$certificate, 'plaintext');
    }


    public function testSignRejectsGarbageSignatureFromAgent(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode('not-a-signature')])),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->expectException(AgentSignatureMismatchException::class);
        $backend->sign(self::$certificate, 'plaintext');
    }


    /**
     * The agent signed with a valid key, but not the one belonging to the certificate we passed in.
     * This is what a mismatched key name -- or a key-name resolver that does not bind to the
     * certificate -- looks like from this side.
     */
    public function testSignRejectsSignatureMadeWithAnotherKey(): void
    {
        $plaintext = 'plaintext';
        $wrongKeySignature = $this->realSignature(
            $plaintext,
            C::DIGEST_SHA256,
            PEMCertificatesMock::OTHER_PRIVATE_KEY,
        );

        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode($wrongKeySignature)])),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->expectException(AgentSignatureMismatchException::class);
        $backend->sign(self::$certificate, $plaintext);
    }


    public function testSignRejectsSignatureOverDifferentPlaintext(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(
                200,
                json_encode(['signature' => base64_encode($this->realSignature('some other data'))]),
            ),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->expectException(AgentSignatureMismatchException::class);
        $backend->sign(self::$certificate, 'the data we asked to be signed');
    }


    public function testSignReturnsTheAgentSignatureUnaltered(): void
    {
        $plaintext = 'canonicalized SignedInfo';
        $signature = $this->realSignature($plaintext);

        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode($signature)])),
        );

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->assertSame($signature, $backend->sign(self::$certificate, $plaintext));
    }


    public function testVerifyDelegatesToLocalOpenSslWithPublicKey(): void
    {
        // Produce a real signature with the private key via local OpenSSL.
        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $plaintext = 'data to sign';
        $signature = $localBackend->sign($privateKey, $plaintext);

        // The agent HTTP client must not be called during verify(); use a mock with expects().
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $backend = $this->makeBackend($httpClient);
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $result = $backend->verify(self::$certificate, $plaintext, $signature);

        $this->assertTrue($result);
    }


    public function testVerifyReturnsFalseForBadSignature(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $backend = $this->makeBackend($httpClient);
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $result = $backend->verify(self::$certificate, 'plaintext', 'badsig');

        $this->assertFalse($result);
    }


    public function testVerifyAcceptsPlainPublicKeyDirectly(): void
    {
        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $plaintext = 'verify with plain pubkey';
        $signature = $localBackend->sign($privateKey, $plaintext);

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);
        $this->assertTrue($backend->verify(self::$publicKey, $plaintext, $signature));
    }


    /**
     * Cloning must give the copy its own local OpenSSL backend, so reconfiguring the clone
     * cannot change the digest the original hashes and verifies with.
     */
    public function testCloneIsolatesLocalBackendState(): void
    {
        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $plaintext = 'data signed with sha256';
        $signature = $localBackend->sign($privateKey, $plaintext);

        $backend = $this->makeBackend();
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $clone = clone $backend;
        $clone->setDigestAlg(C::DIGEST_SHA512);

        $this->assertTrue($backend->verify(self::$certificate, $plaintext, $signature));
    }
}
