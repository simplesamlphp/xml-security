<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\AbstractKeyTransporter;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\KeyNameResolver;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\PrivateKeyAgentEncryptionBackend;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\TokenProvider;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Key\SymmetricKey;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptedType;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement;
use SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\CipherValue;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\XML\xenc11\MGF;

use function base64_encode;
use function bin2hex;
use function dirname;
use function json_encode;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptedKeyTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractEncryptedType::class)]
#[CoversClass(EncryptedKey::class)]
final class EncryptedKeyTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    protected static PrivateKey $privKey;

    /** @var \SimpleSAML\XMLSecurity\Key\PublicKey */
    protected static PublicKey $pubKey;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    protected static X509Certificate $certificate;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptedKey::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptedKey.xml',
        );

        self::$privKey = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::PRIVATE_KEY);
        self::$pubKey = PEMCertificatesMock::getPublicKey(PEMCertificatesMock::PUBLIC_KEY);
        self::$certificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData(
                CipherValue::fromString(
                    '3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s' .
                    '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUd' .
                    'oipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQV6dF9YSJqehuRFCqVJprIDZNfrKnm7Wfw' .
                    'MiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO' .
                    'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLo' .
                    'Cn66P/Zt7I9Q==',
                ),
            ),
            IDValue::fromString('Encrypted_KEY_ID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            StringValue::fromString('some_ENTITY_ID'),
            CarriedKeyName::fromString('Name of the key'),
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            CipherValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
            new ReferenceList([
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                ),
            ]),
        );

        $expectedXml = self::$xmlRepresentation->saveXml(self::$xmlRepresentation->documentElement);
        $this->assertNotFalse($expectedXml);
        $actualXml = strval($encryptedKey);

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $encryptedKey = new EncryptedKey(
            new CipherData(
                CipherValue::fromString(
                    '3W3C4UoWshi02yrqsLC2z8Qr1FjdTz7LV9CvpunilOX4teGKsjKqNbS92DKcXLwS8s' .
                    '4eHBdHejiL1bySDQT5diN/TVo8zz0AmPwX3/eHPQE91NWzceB+yaoEDauMPvi7twUd' .
                    'oipbLZa7cyT4QR+RO9w5P5wf4wDoTPUoQV6dF9YSJqehuRFCqVJprIDZNfrKnm7Wfw' .
                    'MiaMLvaLVdLWgXjuVdiH0lT/F4KJrhJwAnjp57KGn9mhAcwkFe+qDIMSi8Ond6I0FO' .
                    'V3SOx8NxpSTHYfZ4qE1Xn/dvUUXqgRnEFPHAw4JFmJPjgTSCPU6BdwBLzqVjh1pCLo' .
                    'Cn66P/Zt7I9Q==',
                ),
            ),
            IDValue::fromString('Encrypted_KEY_ID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            StringValue::fromString('some_ENTITY_ID'),
            CarriedKeyName::fromString('Name of the key'),
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            CipherValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
            new ReferenceList([
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                ),
            ]),
        );

        // Marshall it to a \Dom\Element
        $encryptedKeyElement = $encryptedKey->toXML();

        $xpCache = XPath::getXPath($encryptedKeyElement);
        // Test for a ReferenceList
        $encryptedKeyElements = XPath::xpQuery(
            $encryptedKeyElement,
            './xenc:ReferenceList',
            $xpCache,
        );
        $this->assertCount(1, $encryptedKeyElements);

        // Test ordering of EncryptedKey contents
        /** @var \Dom\Element[] $encryptedKeyElements */
        $encryptedKeyElements = XPath::xpQuery(
            $encryptedKeyElement,
            './xenc:ReferenceList/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(1, $encryptedKeyElements);
        $this->assertEquals('xenc:CarriedKeyName', $encryptedKeyElements[0]->tagName);
    }


    /**
     * Test encryption and decryption with PKCS1 RSA 1.5.
     */
    public function testPKCS1Encryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        /** @var \SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmInterface $encryptor */
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(8);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_RSA_1_5),
            ),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_RSA_1_5, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }


    /**
     * Test encryption and decryption with RSA OAEP
     */
    public function testOAEPEncryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(16);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
            ),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }


    /**
     * Test encryption and decryption with RSA OAEP-MGF1P
     */
    public function testOAEMGF1PPEncryption(): void
    {
        $factory = new KeyTransportAlgorithmFactory([]);
        $encryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$pubKey);
        $symmetricKey = SymmetricKey::generate(16);
        $encryptedKey = EncryptedKey::fromKey(
            $symmetricKey,
            $encryptor,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP_MGF1P),
            ),
        );

        $decryptor = $factory->getAlgorithm(C::KEY_TRANSPORT_OAEP_MGF1P, self::$privKey);
        $decryptedKey = $encryptedKey->decrypt($decryptor);

        $this->assertEquals(bin2hex($symmetricKey->getMaterial()), bin2hex($decryptedKey));
    }


    /**
     * Verify that decrypt() forwards the DigestMethod and MGF URIs to the decryptor when present.
     *
     * @dataProvider oaepReadPathProvider
     */
    #[DataProvider('oaepReadPathProvider')]
    public function testDecryptForwardsOaepParamsToAwareDecryptor(
        string $cipherAlg,
        ?string $digestAlg,
        ?string $mgfAlg,
    ): void {
        // Mock a decryptor implementing both EncryptionAlgorithmInterface and OAEPParametersAware.
        $decryptor = $this->createMock(OaepAwareDecryptorInterface::class);
        $decryptor->method('getAlgorithmId')->willReturn($cipherAlg);
        $decryptor->expects($this->once())
            ->method('setOAEPParams')
            ->with($digestAlg, $mgfAlg);
        $decryptor->method('decrypt')->willReturn('decrypted');

        $children = [];
        if ($digestAlg !== null) {
            $children[] = new DigestMethod(AnyURIValue::fromString($digestAlg));
        }
        if ($mgfAlg !== null) {
            $children[] = new MGF(AnyURIValue::fromString($mgfAlg));
        }

        $encryptedKey = new EncryptedKey(
            new CipherData(
                CipherValue::fromString(base64_encode('fake-ciphertext')),
            ),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString($cipherAlg),
                null,
                null,
                $children,
            ),
        );

        $encryptedKey->decrypt($decryptor);
    }


    /** @return array<string, array{string, string|null, string|null}> */
    public static function oaepReadPathProvider(): array
    {
        $sha1   = C::DIGEST_SHA1;
        $sha256 = C::DIGEST_SHA256;
        $sha384 = C::DIGEST_SHA384;
        $sha512 = C::DIGEST_SHA512;
        $mgf1   = 'http://www.w3.org/2009/xmlenc11#mgf1sha1';
        $mgf256 = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';
        $mgf384 = 'http://www.w3.org/2009/xmlenc11#mgf1sha384';
        $mgf512 = 'http://www.w3.org/2009/xmlenc11#mgf1sha512';

        return [
            'no-children'         => [C::KEY_TRANSPORT_OAEP, null,    null],
            'sha256-mgf256'       => [C::KEY_TRANSPORT_OAEP, $sha256, $mgf256],
            'sha384-mgf384'       => [C::KEY_TRANSPORT_OAEP, $sha384, $mgf384],
            'sha512-mgf512'       => [C::KEY_TRANSPORT_OAEP, $sha512, $mgf512],
            'sha1-mgf1'           => [C::KEY_TRANSPORT_OAEP, $sha1,   $mgf1],
            'mgf1p-no-children'   => [C::KEY_TRANSPORT_OAEP_MGF1P, null, null],
        ];
    }


    /**
     * Verify that decrypt() still forwards the correct DigestMethod/MGF Algorithm URIs when
     * those children are unresolved SimpleSAML\XML\Chunk objects (simulating a deployment
     * where the xml-common element registry has not registered the typed ds\DigestMethod /
     * xenc11\MGF classes), instead of silently leaving digestAlg/mgf null.
     */
    public function testDecryptForwardsOaepParamsFromUnregisteredChunkChildren(): void
    {
        $digestAlg = C::DIGEST_SHA256;
        $mgfAlg = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';

        $decryptor = $this->createMock(OaepAwareDecryptorInterface::class);
        $decryptor->method('getAlgorithmId')->willReturn(C::KEY_TRANSPORT_OAEP);
        $decryptor->expects($this->once())
            ->method('setOAEPParams')
            ->with($digestAlg, $mgfAlg);
        $decryptor->method('decrypt')->willReturn('decrypted');

        $digestChunk = new Chunk(DOMDocumentFactory::fromString(
            '<ds:DigestMethod xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Algorithm="' . $digestAlg . '"/>',
        )->documentElement);
        $mgfChunk = new Chunk(DOMDocumentFactory::fromString(
            '<xenc11:MGF xmlns:xenc11="http://www.w3.org/2009/xmlenc11#" Algorithm="' . $mgfAlg . '"/>',
        )->documentElement);

        $encryptedKey = new EncryptedKey(
            new CipherData(
                CipherValue::fromString(base64_encode('fake-ciphertext')),
            ),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
                null,
                null,
                [$digestChunk, $mgfChunk],
            ),
        );

        $encryptedKey->decrypt($decryptor);
    }


    /**
     * Verify that an unrelated Chunk child (neither DigestMethod nor MGF) does not interfere
     * with the OAEP read-out, digestAlg/mgf remain null.
     */
    public function testDecryptIgnoresUnrelatedChunkChild(): void
    {
        $decryptor = $this->createMock(OaepAwareDecryptorInterface::class);
        $decryptor->method('getAlgorithmId')->willReturn(C::KEY_TRANSPORT_OAEP);
        $decryptor->expects($this->once())
            ->method('setOAEPParams')
            ->with(null, null);
        $decryptor->method('decrypt')->willReturn('decrypted');

        $unrelatedChunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        )->documentElement);

        $encryptedKey = new EncryptedKey(
            new CipherData(
                CipherValue::fromString(base64_encode('fake-ciphertext')),
            ),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
                null,
                null,
                [$unrelatedChunk],
            ),
        );

        $encryptedKey->decrypt($decryptor);
    }


    /**
     * Verify that a non-OAEPParametersAware decryptor is not passed OAEP params
     * (existing behavior preserved for such decryptors).
     */
    public function testDecryptDoesNotCallSetOaepParamsOnNonAwareDecryptor(): void
    {
        // A stub that does NOT implement OAEPParametersAware
        $decryptor = $this->createStub(NonAwareDecryptorInterface::class);
        $decryptor->method('getAlgorithmId')->willReturn(C::KEY_TRANSPORT_OAEP);
        $decryptor->method('decrypt')->willReturn('decrypted');

        $encryptedKey = new EncryptedKey(
            new CipherData(CipherValue::fromString(base64_encode('ciphertext'))),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
                null,
                null,
                [
                    new DigestMethod(AnyURIValue::fromString(C::DIGEST_SHA256)),
                    new MGF(AnyURIValue::fromString('http://www.w3.org/2009/xmlenc11#mgf1sha256')),
                ],
            ),
        );

        // Should not throw; children are simply ignored for non-aware decryptors.
        $result = $encryptedKey->decrypt($decryptor);
        $this->assertSame('decrypted', $result);
    }


    /**
     * Verify that non-null OAEP params on a non-OAEPParametersAware backend
     * (set via AbstractKeyTransporter) cause UnsupportedAlgorithmException.
     */
    public function testNonAwareBackendWithNonNullOaepParamsThrows(): void
    {
        // Create a mock backend that does NOT implement OAEPParametersAware.
        $nonAwareBackend = $this->createStub(EncryptionBackend::class);
        $nonAwareBackend->method('encrypt')->willReturn('enc');
        $nonAwareBackend->method('decrypt')->willReturn('dec');

        // Create a transporter subclass with X509Certificate support.
        $transporter = new class (self::$certificate, C::KEY_TRANSPORT_OAEP) extends AbstractKeyTransporter {
            /** @return string[] */
            public static function getSupportedAlgorithms(): array
            {
                return C::$KEY_TRANSPORT_ALGORITHMS;
            }
        };
        $transporter->setBackend($nonAwareBackend);

        $encryptedKey = new EncryptedKey(
            new CipherData(CipherValue::fromString(base64_encode('ciphertext'))),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
                null,
                null,
                [
                    new DigestMethod(AnyURIValue::fromString(C::DIGEST_SHA256)),
                    new MGF(AnyURIValue::fromString('http://www.w3.org/2009/xmlenc11#mgf1sha256')),
                ],
            ),
        );

        $this->expectException(UnsupportedAlgorithmException::class);
        $encryptedKey->decrypt($transporter);
    }


    /**
     * End-to-end: with PrivateKeyAgentEncryptionBackend as the backend of a transporter,
     * the correct agent algorithm is selected based on the DigestMethod/MGF children.
     *
     * @dataProvider endToEndOaepProvider
     */
    #[DataProvider('endToEndOaepProvider')]
    public function testDecryptWithPkaBackendUsesCorrectAgentAlgorithm(
        ?string $digestAlg,
        ?string $mgfAlg,
        string $expectedAgentAlgorithm,
    ): void {
        $capturedBody = null;
        $httpClient = $this->createStub(ClientInterface::class);
        $requestFactory = $this->createStub(RequestFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $request = $this->createStub(RequestInterface::class);
        $request->method('withHeader')->willReturn($request);
        $request->method('withBody')->willReturn($request);
        $requestFactory->method('createRequest')->willReturn($request);
        $streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody): StreamInterface {
                $capturedBody = $body;
                return $this->createStub(StreamInterface::class);
            },
        );

        $plaintext = 'symmetric-key-material';
        $responseStream = $this->createStub(StreamInterface::class);
        $responseStream->method('__toString')->willReturn(
            json_encode(['decrypted_data' => base64_encode($plaintext)]),
        );
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseStream);
        $httpClient->method('sendRequest')->willReturn($response);

        $tokenProvider = $this->createStub(TokenProvider::class);
        $tokenProvider->method('getToken')->willReturn('test-token');
        $keyNameResolver = $this->createStub(KeyNameResolver::class);
        $keyNameResolver->method('resolve')->willReturn('test-key');

        $pkaBackend = new PrivateKeyAgentEncryptionBackend(
            $httpClient,
            $requestFactory,
            $streamFactory,
            'https://agent.example.com',
            $tokenProvider,
            $keyNameResolver,
        );

        // Create a transporter with the certificate and PKA backend.
        $transporter = new class (self::$certificate, C::KEY_TRANSPORT_OAEP) extends AbstractKeyTransporter {
            /** @return string[] */
            public static function getSupportedAlgorithms(): array
            {
                return C::$KEY_TRANSPORT_ALGORITHMS;
            }
        };
        $transporter->setBackend($pkaBackend);

        $children = [];
        if ($digestAlg !== null) {
            $children[] = new DigestMethod(AnyURIValue::fromString($digestAlg));
        }
        if ($mgfAlg !== null) {
            $children[] = new MGF(AnyURIValue::fromString($mgfAlg));
        }

        $encryptedKey = new EncryptedKey(
            new CipherData(CipherValue::fromString(base64_encode('fake-rsa-ciphertext'))),
            null,
            null,
            null,
            null,
            null,
            null,
            new EncryptionMethod(
                AnyURIValue::fromString(C::KEY_TRANSPORT_OAEP),
                null,
                null,
                $children,
            ),
        );

        $encryptedKey->decrypt($transporter);

        $this->assertNotNull($capturedBody);
        $decoded = json_decode($capturedBody, true);
        $this->assertSame($expectedAgentAlgorithm, $decoded['algorithm']);
    }


    /** @return array<string, array{string|null, string|null, string}> */
    public static function endToEndOaepProvider(): array
    {
        $sha1   = C::DIGEST_SHA1;
        $sha256 = C::DIGEST_SHA256;
        $sha384 = C::DIGEST_SHA384;
        $sha512 = C::DIGEST_SHA512;
        $mgf1   = 'http://www.w3.org/2009/xmlenc11#mgf1sha1';
        $mgf256 = 'http://www.w3.org/2009/xmlenc11#mgf1sha256';
        $mgf384 = 'http://www.w3.org/2009/xmlenc11#mgf1sha384';
        $mgf512 = 'http://www.w3.org/2009/xmlenc11#mgf1sha512';

        return [
            'both-absent-default-sha256' => [null,    null,    'rsa-pkcs1-oaep-mgf1-sha256'],
            'sha1-mgf1'                  => [$sha1,   $mgf1,   'rsa-pkcs1-oaep-mgf1-sha1'],
            'sha256-mgf256'              => [$sha256, $mgf256, 'rsa-pkcs1-oaep-mgf1-sha256'],
            'sha384-mgf384'              => [$sha384, $mgf384, 'rsa-pkcs1-oaep-mgf1-sha384'],
            'sha512-mgf512'              => [$sha512, $mgf512, 'rsa-pkcs1-oaep-mgf1-sha512'],
        ];
    }
}
