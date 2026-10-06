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
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentEncryptionBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\TokenProvider;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\AgentUnavailableException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

use function base64_encode;
use function json_decode;
use function json_encode;

/**
 * Tests for PrivateKeyAgentEncryptionBackend.
 *
 * @package simplesamlphp/xml-security
 */
final class PrivateKeyAgentEncryptionBackendTest extends TestCase
{
    private const string BASE_URL = 'https://agent.example.com';

    private const string KEY_NAME = 'my-decrypt-key';

    private const string TOKEN = 'decrypt-token';

    // MGF URIs for OAEP tests
    private const string MGF1_SHA1   = 'http://www.w3.org/2009/xmlenc11#mgf1sha1';

    private const string MGF1_SHA224 = 'http://www.w3.org/2009/xmlenc11#mgf1sha224';

    private const string MGF1_SHA256 = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';

    private const string MGF1_SHA384 = 'http://www.w3.org/2009/xmlenc11#mgf1sha384';

    private const string MGF1_SHA512 = 'http://www.w3.org/2009/xmlenc11#mgf1sha512';


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


    private function makeBackend(?ClientInterface $httpClient = null): PrivateKeyAgentEncryptionBackend
    {
        return new PrivateKeyAgentEncryptionBackend(
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


    public function testSetCipherAcceptsKeyTransportAlgorithms(): void
    {
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $backend->setCipher(C::KEY_TRANSPORT_RSA_1_5);
        $this->addToAssertionCount(1); // Verifies none of the above threw an exception.
    }


    public function testSetCipherRejectsBlockCiphers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeBackend()->setCipher(C::BLOCK_ENC_AES256_GCM);
    }


    public function testSetCipherRejectsUnknownCipher(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeBackend()->setCipher('http://example.com/unknown-cipher');
    }


    public function testSetOAEPParamsBeforeCipherThrowsRuntimeException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->makeBackend()->setOAEPParams(C::DIGEST_SHA256, self::MGF1_SHA256);
    }


    public function testSetOAEPParamsWithNullsIsAlwaysAccepted(): void
    {
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(null, null);
        $this->addToAssertionCount(1); // Verifies null/null does not throw.
    }


    /**
     * Full OAEP matrix: correct agent algorithm per digest/mgf combination.
     *
     * @dataProvider oaepMatrixProvider
     */
    #[DataProvider('oaepMatrixProvider')]
    public function testSetOAEPParamsAndDecryptUsesCorrectAgentAlgorithm(
        string $cipher,
        ?string $digestAlg,
        ?string $mgf,
        string $expectedAgentAlgorithm,
    ): void {
        $plaintext = 'decrypted-key-bytes';
        $capturedBody = null;
        $this->streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['decrypted_data' => base64_encode($plaintext)])),
        );

        $backend = $this->makeBackend();
        $backend->setCipher($cipher);
        $backend->setOAEPParams($digestAlg, $mgf);
        $backend->decrypt(self::$certificate, 'fake-ciphertext');

        $decoded = json_decode($capturedBody, true);
        $this->assertSame($expectedAgentAlgorithm, $decoded['algorithm']);
    }


    /** @return array<string, array{string, string|null, string|null, string}> */
    public static function oaepMatrixProvider(): array
    {
        $sha1   = C::DIGEST_SHA1;
        $sha224 = C::DIGEST_SHA224;
        $sha256 = C::DIGEST_SHA256;
        $sha384 = C::DIGEST_SHA384;
        $sha512 = C::DIGEST_SHA512;

        $mgf1   = self::MGF1_SHA1;
        $mgf224 = self::MGF1_SHA224;
        $mgf256 = self::MGF1_SHA256;
        $mgf384 = self::MGF1_SHA384;
        $mgf512 = self::MGF1_SHA512;

        return [
            // rsa-oaep-mgf1p: absent or SHA-1 only
            'mgf1p-no-params'       => [C::KEY_TRANSPORT_OAEP_MGF1P, null,    null,    'rsa-pkcs1-oaep-mgf1-sha1'],
            'mgf1p-explicit-sha1'   => [C::KEY_TRANSPORT_OAEP_MGF1P, $sha1,   null,    'rsa-pkcs1-oaep-mgf1-sha1'],
            'mgf1p-sha1-mgf1sha1'   => [C::KEY_TRANSPORT_OAEP_MGF1P, $sha1,   $mgf1,   'rsa-pkcs1-oaep-mgf1-sha1'],
            // rsa-oaep (xmlenc11): both absent → default SHA-256
            'oaep-both-absent'      => [C::KEY_TRANSPORT_OAEP, null,    null,    'rsa-pkcs1-oaep-mgf1-sha256'],
            // rsa-oaep: explicit digest+mgf pairs
            'oaep-sha1-mgf1sha1'    => [C::KEY_TRANSPORT_OAEP, $sha1,   $mgf1,   'rsa-pkcs1-oaep-mgf1-sha1'],
            'oaep-sha224-mgf1sha224' => [C::KEY_TRANSPORT_OAEP, $sha224, $mgf224, 'rsa-pkcs1-oaep-mgf1-sha224'],
            'oaep-sha256-mgf1sha256' => [C::KEY_TRANSPORT_OAEP, $sha256, $mgf256, 'rsa-pkcs1-oaep-mgf1-sha256'],
            'oaep-sha384-mgf1sha384' => [C::KEY_TRANSPORT_OAEP, $sha384, $mgf384, 'rsa-pkcs1-oaep-mgf1-sha384'],
            'oaep-sha512-mgf1sha512' => [C::KEY_TRANSPORT_OAEP, $sha512, $mgf512, 'rsa-pkcs1-oaep-mgf1-sha512'],
        ];
    }


    /**
     * mgf1p with a non-SHA-1 digest must fail-closed.
     */
    public function testOaepMgf1pWithNonSha1DigestThrows(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $backend->setOAEPParams(C::DIGEST_SHA256, null);
    }


    /**
     * rsa-oaep with exactly one of digest/mgf must fail-closed.
     */
    public function testOaepWithOnlyDigestThrows(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(C::DIGEST_SHA256, null);
    }


    public function testOaepWithOnlyMgfThrows(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(null, self::MGF1_SHA256);
    }


    /**
     * rsa-oaep with mismatched digest/mgf must fail-closed.
     */
    public function testOaepWithMismatchedDigestMgfThrows(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        // SHA-256 digest with SHA-384 MGF → mismatch
        $backend->setOAEPParams(C::DIGEST_SHA256, self::MGF1_SHA384);
    }


    /**
     * setCipher() must reset OAEP params so the next setOAEPParams() starts fresh.
     */
    public function testSetCipherResetsOaepParams(): void
    {
        $capturedBody = null;
        $this->streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(200, json_encode(['decrypted_data' => base64_encode('plain')])),
        );

        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(C::DIGEST_SHA512, self::MGF1_SHA512);
        // Re-configure cipher → OAEP params must be reset to null
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        // Now decrypt without calling setOAEPParams() → should use the default (both absent = sha256)
        $backend->decrypt(self::$certificate, 'ciphertext');

        $decoded = json_decode($capturedBody, true);
        $this->assertSame('rsa-pkcs1-oaep-mgf1-sha256', $decoded['algorithm']);
    }


    public function testDecryptRejectsNonCertificateKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->decrypt(self::$publicKey, 'ciphertext');
    }


    public function testDecryptRejectsSymmetricKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->decrypt(SymmetricKey::generate(16), 'ciphertext');
    }


    public function testDecryptPropagatesAgentException(): void
    {
        $this->httpClient->method('sendRequest')->willReturn(
            $this->makeResponse(503, ''),
        );

        $this->expectException(AgentUnavailableException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->decrypt(self::$certificate, 'ciphertext');
    }


    public function testEncryptDelegatesToLocalOpenSslWithPublicKey(): void
    {
        // The agent HTTP client must not be called during encrypt(); use a mock with expects().
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $backend = $this->makeBackend($httpClient);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);

        // Encrypt using the certificate; it must unwrap the public key.
        $plaintext = 'key-material-to-encrypt';
        $ciphertext = $backend->encrypt(self::$certificate, $plaintext);

        // The result should be non-empty ciphertext (RSA-encrypted).
        $this->assertNotEmpty($ciphertext);
        $this->assertNotSame($plaintext, $ciphertext);
    }


    public function testEncryptWorksWithPlainPublicKeyToo(): void
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->never())->method('sendRequest');

        $backend = $this->makeBackend($httpClient);
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);

        $ciphertext = $backend->encrypt(self::$publicKey, 'test-data');
        $this->assertNotEmpty($ciphertext);
    }


    public function testEncryptWithNonSha1OaepParamsThrows(): void
    {
        $this->expectException(UnsupportedAlgorithmException::class);
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(C::DIGEST_SHA256, self::MGF1_SHA256);
        $backend->encrypt(self::$certificate, 'plaintext');
    }


    public function testEncryptWithSha1OaepParamsSucceeds(): void
    {
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP);
        $backend->setOAEPParams(C::DIGEST_SHA1, self::MGF1_SHA1);

        $plaintext = 'test-data';
        $ciphertext = $backend->encrypt(self::$certificate, $plaintext);

        $this->assertNotEmpty($ciphertext);
        $this->assertNotSame($plaintext, $ciphertext);
    }


    /**
     * Cloning must give the copy its own local OpenSSL backend, so reconfiguring the clone
     * cannot change what the original encrypts with.
     */
    public function testCloneIsolatesLocalBackendState(): void
    {
        $backend = $this->makeBackend();
        $backend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);

        $clone = clone $backend;
        $clone->setCipher(C::KEY_TRANSPORT_RSA_1_5);

        $plaintext  = 'session key material';
        $ciphertext = $backend->encrypt(self::$certificate, $plaintext);

        // Round-trips only if the original still encrypts with OAEP-MGF1P.
        $localBackend = new OpenSSL();
        $localBackend->setCipher(C::KEY_TRANSPORT_OAEP_MGF1P);
        $privateKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);

        $this->assertSame($plaintext, $localBackend->decrypt($privateKey, $ciphertext));
    }
}
