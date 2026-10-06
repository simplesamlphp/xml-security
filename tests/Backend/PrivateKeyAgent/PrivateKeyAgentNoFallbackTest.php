<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\PrivateKeyAgentRSA as KeyTransportPrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Alg\Signature\PrivateKeyAgentRSA as SignaturePrivateKeyAgentRSA;
use SimpleSAML\XMLSecurity\Backend\OpenSSL;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\KeyNameResolver;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentEncryptionBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentSignatureBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\TokenProvider;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\AgentUnavailableException;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function base64_encode;
use function file_get_contents;
use function glob;
use function hash;
use function json_decode;
use function json_encode;

/**
 * Security-focused no-fallback acceptance tests for the Private Key Agent backends.
 *
 * Verifies that:
 * - Sign/decrypt are round-trippable with a simulated agent response.
 * - The agent HTTP client is called exactly once per operation and never for verify/encrypt.
 * - No private-key-loading call appears anywhere in the PKA backend source files.
 * - A failing agent produces a controlled exception and never silently falls back to local
 *   private-key operations, including through the PrivateKeyAgentRSA wrapper routing.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentNoFallbackTest extends TestCase
{
    private const string BASE_URL = 'https://agent.example.com';

    private const string KEY_NAME  = 'test-key';

    private const string TOKEN     = 'test-token';


    private RequestFactoryInterface&Stub $requestFactory;

    private StreamFactoryInterface&Stub $streamFactory;

    private RequestInterface&Stub $request;

    private TokenProvider&Stub $tokenProvider;

    private KeyNameResolver&Stub $keyNameResolver;


    protected function setUp(): void
    {
        $this->requestFactory = $this->createStub(RequestFactoryInterface::class);
        $this->streamFactory  = $this->createStub(StreamFactoryInterface::class);
        $this->request        = $this->createStub(RequestInterface::class);

        $this->request->method('withHeader')->willReturn($this->request);
        $this->request->method('withBody')->willReturn($this->request);
        $this->requestFactory->method('createRequest')->willReturn($this->request);
        $this->streamFactory->method('createStream')->willReturn($this->createStub(StreamInterface::class));

        $this->tokenProvider = $this->createStub(TokenProvider::class);
        $this->tokenProvider->method('getToken')->willReturn(self::TOKEN);

        $this->keyNameResolver = $this->createStub(KeyNameResolver::class);
        $this->keyNameResolver->method('resolve')->willReturn(self::KEY_NAME);
    }


    private function makeSignatureBackend(?ClientInterface $httpClient = null): PrivateKeyAgentSignatureBackend
    {
        return new PrivateKeyAgentSignatureBackend(
            $httpClient ?? $this->createStub(ClientInterface::class),
            $this->requestFactory,
            $this->streamFactory,
            self::BASE_URL,
            $this->tokenProvider,
            $this->keyNameResolver,
        );
    }


    private function makeEncryptionBackend(?ClientInterface $httpClient = null): PrivateKeyAgentEncryptionBackend
    {
        return new PrivateKeyAgentEncryptionBackend(
            $httpClient ?? $this->createStub(ClientInterface::class),
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


    public function testSignRoundTripWithSimulatedAgentResponse(): void
    {
        $plaintext = 'canonicalized SignedInfo content';

        // Compute the real RSA-PKCS1v1.5-SHA256 signature the agent would produce.
        $privateKey   = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $realSignature = $localBackend->sign($privateKey, $plaintext);

        // HTTP client must be called exactly once (the sign POST).
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->once())->method('sendRequest')
            ->willReturn(
                $this->makeResponse(200, json_encode(['signature' => base64_encode($realSignature)])),
            );

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeSignatureBackend($httpClient);
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $signature = $backend->sign($cert, $plaintext);

        // Verify locally with the public key extracted from the certificate.
        $this->assertTrue($localBackend->verify($cert->getPublicKey(), $plaintext, $signature));
    }


    public function testDecryptRoundTripWithSimulatedAgentResponse(): void
    {
        $plaintext = 'secret symmetric key material';

        // HTTP client must be called exactly once (the decrypt POST).
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->once())->method('sendRequest')
            ->willReturn(
                $this->makeResponse(200, json_encode(['decrypted_data' => base64_encode($plaintext)])),
            );

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeEncryptionBackend($httpClient);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);

        // Encrypt ciphertext locally with the public key (the actual value doesn't matter here;
        // the simulated agent always returns the fixed plaintext regardless of input).
        $localBackend = new OpenSSL();
        $localBackend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $ciphertext = $localBackend->encrypt($cert->getPublicKey(), $plaintext);

        $result = $backend->decrypt($cert, $ciphertext);

        $this->assertSame($plaintext, $result);
    }


    public function testVerifyNeverCallsAgent(): void
    {
        $privateKey   = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $plaintext = 'data for verify';
        $signature = $localBackend->sign($privateKey, $plaintext);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeSignatureBackend($httpClient);
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->assertTrue($backend->verify($cert, $plaintext, $signature));
    }


    public function testEncryptNeverCallsAgent(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeEncryptionBackend($httpClient);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);

        $result = $backend->encrypt($cert, 'plaintext');
        $this->assertNotEmpty($result);
    }


    public function testPkaBackendSourceFilesDoNotLoadPrivateKeys(): void
    {
        $dir   = __DIR__ . '/../../../src/Backend/PrivateKeyAgent/';
        $files = glob($dir . '*.php') ?: [];

        $this->assertNotEmpty($files, 'Expected PHP source files in ' . $dir);

        foreach ($files as $file) {
            $source = file_get_contents($file);
            $this->assertIsString($source);

            $basename = basename($file);
            $this->assertStringNotContainsString(
                'PrivateKey::fromFile(',
                $source,
                "Found PrivateKey::fromFile() (private-key loading) in {$basename}",
            );
            $this->assertStringNotContainsString(
                'new PrivateKey(',
                $source,
                "Found new PrivateKey() (private-key instantiation) in {$basename}",
            );
            $this->assertStringNotContainsString(
                'openssl_pkey_get_private(',
                $source,
                "Found openssl_pkey_get_private() (raw private-key loading) in {$basename}",
            );
        }
    }


    public function testFailingAgentThrowsAgentUnavailableExceptionForCertKey(): void
    {
        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturn($this->makeResponse(503, 'Service Unavailable'));

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeSignatureBackend($httpClient);
        $backend->setDigestAlg(C::DIGEST_SHA256);

        $this->expectException(AgentUnavailableException::class);
        $backend->sign($cert, 'test data');
    }


    public function testFailingAgentDecryptThrowsAgentUnavailableExceptionForCertKey(): void
    {
        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturn($this->makeResponse(503, ''));

        $cert    = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $backend = $this->makeEncryptionBackend($httpClient);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);

        $this->expectException(AgentUnavailableException::class);
        $backend->decrypt($cert, 'ciphertext');
    }


    /**
     * Verify that the PrivateKeyAgentRSA signature wrapper never falls back to a local
     * private-key operation when the agent fails: a certificate key always routes to the
     * PKA backend, so a backend failure must propagate as-is.
     */
    public function testSignatureWrapperWithCertKeyNeverFallsBackToLocalOnAgentFailure(): void
    {
        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturn($this->makeResponse(503, ''));

        $cert       = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->makeSignatureBackend($httpClient);
        $pkaBackend->setDigestAlg(C::DIGEST_SHA256);

        $wrapper = new SignaturePrivateKeyAgentRSA($cert, C::SIG_RSA_SHA256, $pkaBackend);

        $this->expectException(AgentUnavailableException::class);
        $wrapper->sign('data');
    }


    /**
     * Verify that the PrivateKeyAgentRSA key-transport wrapper never falls back to a local
     * private-key operation when the agent fails.
     */
    public function testKeyTransportWrapperWithCertKeyNeverFallsBackToLocalOnAgentFailure(): void
    {
        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturn($this->makeResponse(503, ''));

        $cert       = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->makeEncryptionBackend($httpClient);
        $pkaBackend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);

        $wrapper = new KeyTransportPrivateKeyAgentRSA($cert, C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);

        $this->expectException(AgentUnavailableException::class);
        $wrapper->decrypt('ciphertext');
    }


    /**
     * Replace the stream factory with one that records every body handed to it, so the JSON
     * request sent to the agent can be inspected.
     *
     * @param string|null $capturedBody Receives the last body passed to createStream().
     */
    private function captureRequestBody(?string &$capturedBody): void
    {
        $this->streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );
    }


    /**
     * Two signature wrappers built from a single registered backend must not share its state.
     *
     * This is the boot-time registration pattern: one backend instance is captured in the closure
     * passed to registerAlgorithmFactory() and reused for every algorithm the factory hands out.
     * Because SignableElementTrait::sign() only stores the signer and the signature is produced
     * later, at toXML() time, several signers are routinely alive at once. A shared backend would
     * let the last one constructed dictate the digest for all of them, producing a signature that
     * does not match the SignatureMethod declared in the XML.
     */
    public function testSignatureWrappersBuiltFromOneBackendDoNotShareDigestState(): void
    {
        $plaintext = 'canonicalized SignedInfo content';

        $privateKey   = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        $localBackend = new OpenSSL();
        $localBackend->setDigestAlg(C::DIGEST_SHA256);
        $realSignature = $localBackend->sign($privateKey, $plaintext);

        $capturedBody = null;
        $this->captureRequestBody($capturedBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['signature' => base64_encode($realSignature)])),
        );

        $cert       = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->makeSignatureBackend($httpClient);

        $sha256Signer = new SignaturePrivateKeyAgentRSA($cert, C::SIG_RSA_SHA256, $pkaBackend);
        // Constructed after the SHA-256 signer, and still pending when that one signs.
        new SignaturePrivateKeyAgentRSA($cert, C::SIG_RSA_SHA512, $pkaBackend);

        $signature = $sha256Signer->sign($plaintext);

        $decoded = json_decode($capturedBody, true);
        $this->assertSame('rsa-pkcs1-v1_5-sha256', $decoded['algorithm']);
        $this->assertSame(base64_encode(hash('sha256', $plaintext, binary: true)), $decoded['hash']);
        $this->assertTrue($localBackend->verify($cert->getPublicKey(), $plaintext, $signature));
    }


    /**
     * Two key-transport wrappers built from a single registered backend must not share its cipher.
     */
    public function testKeyTransportWrappersBuiltFromOneBackendDoNotShareCipherState(): void
    {
        $capturedBody = null;
        $this->captureRequestBody($capturedBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['decrypted_data' => base64_encode('session key')])),
        );

        $cert       = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->makeEncryptionBackend($httpClient);

        $mgf1pDecryptor = new KeyTransportPrivateKeyAgentRSA($cert, C::KEY_TRANSPORT_OAEP_MGF1P, $pkaBackend);
        // Constructed afterwards; its cipher must not leak into the mgf1p decryptor.
        new KeyTransportPrivateKeyAgentRSA($cert, C::KEY_TRANSPORT_OAEP, $pkaBackend);

        $this->assertSame('session key', $mgf1pDecryptor->decrypt('ciphertext'));

        $decoded = json_decode($capturedBody, true);
        $this->assertSame('rsa-pkcs1-oaep-mgf1-sha1', $decoded['algorithm']);
    }


    /**
     * OAEP parameters read from one EncryptedKey must not leak into another decryptor built from
     * the same registered backend.
     */
    public function testKeyTransportWrappersBuiltFromOneBackendDoNotShareOaepParameters(): void
    {
        $capturedBody = null;
        $this->captureRequestBody($capturedBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['decrypted_data' => base64_encode('session key')])),
        );

        $cert       = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        $pkaBackend = $this->makeEncryptionBackend($httpClient);

        $defaultDecryptor = new KeyTransportPrivateKeyAgentRSA($cert, C::KEY_TRANSPORT_OAEP, $pkaBackend);
        $sha512Decryptor  = new KeyTransportPrivateKeyAgentRSA($cert, C::KEY_TRANSPORT_OAEP, $pkaBackend);

        // As EncryptedKey::decrypt() would do for an EncryptedKey carrying explicit OAEP parameters.
        $sha512Decryptor->setOAEPParams(C::DIGEST_SHA512, 'http://www.w3.org/2009/xmlenc11#mgf1sha512');

        $defaultDecryptor->decrypt('ciphertext');

        $decoded = json_decode($capturedBody, true);
        $this->assertSame('rsa-pkcs1-oaep-mgf1-sha256', $decoded['algorithm']);
    }
}
