<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\FingerprintKeyNameResolver;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnknownKeyException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for FingerprintKeyNameResolver.
 *
 * Fixtures use only certificates (public side); no private key material is referenced.
 *
 * @package simplesamlphp/xml-security
 */
final class FingerprintKeyNameResolverTest extends TestCase
{
    /** SHA-256 fingerprint of signed.simplesamlphp.org.crt (from openssl x509 -fingerprint -sha256). */
    private const string CERT_FP = 'ead377f50f5de887af94058b78cee54066b352756524e7b5c02937b2ba36adf1';

    /** SHA-256 fingerprint of other.simplesamlphp.org.crt. */
    private const string OTHER_FP = 'c3d8f918c5c20e3daf53b749d66a3b06ff11ac67700e0c1fb609c002609721c6';


    private static X509Certificate $certificate;

    private static X509Certificate $otherCertificate;


    public static function setUpBeforeClass(): void
    {
        // Only certificates, no private key material used in fixtures.
        self::$certificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
        self::$otherCertificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::OTHER_CERTIFICATE);
    }


    /**
     * Test that a known fingerprint is resolved to its key name.
     */
    public function testResolveKnownFingerprint(): void
    {
        $resolver = new FingerprintKeyNameResolver([
            self::CERT_FP => 'signing-key',
        ]);
        $this->assertSame('signing-key', $resolver->resolve(self::$certificate));
    }


    /**
     * Test that multiple fingerprints resolve to their respective key names.
     */
    public function testResolveMultipleFingerprints(): void
    {
        $resolver = new FingerprintKeyNameResolver([
            self::CERT_FP  => 'key-a',
            self::OTHER_FP => 'key-b',
        ]);
        $this->assertSame('key-a', $resolver->resolve(self::$certificate));
        $this->assertSame('key-b', $resolver->resolve(self::$otherCertificate));
    }


    /**
     * Test that resolving an unknown fingerprint throws UnknownKeyException.
     */
    public function testUnknownFingerprintThrows(): void
    {
        $resolver = new FingerprintKeyNameResolver([
            self::OTHER_FP => 'key-b',
        ]);
        $this->expectException(UnknownKeyException::class);
        $resolver->resolve(self::$certificate);
    }


    /**
     * Test that an invalid key name in the map throws InvalidArgumentException at construction.
     */
    public function testInvalidKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FingerprintKeyNameResolver([
            self::CERT_FP => 'has/slash',
        ]);
    }


    /**
     * Test that a key name exceeding 64 characters throws InvalidArgumentException at construction.
     */
    public function testTooLongKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FingerprintKeyNameResolver([
            self::CERT_FP => str_repeat('x', 65),
        ]);
    }


    /**
     * Test that an empty map is valid (no fingerprints → any resolve() call throws UnknownKeyException).
     */
    public function testEmptyMapResolvesUnknown(): void
    {
        $resolver = new FingerprintKeyNameResolver([]);
        $this->expectException(UnknownKeyException::class);
        $resolver->resolve(self::$certificate);
    }


    /**
     * Test that an uppercase fingerprint in the map resolves the same certificate as its lowercase form.
     */
    public function testUppercaseFingerprintResolves(): void
    {
        $resolver = new FingerprintKeyNameResolver([
            strtoupper(self::CERT_FP) => 'signing-key',
        ]);
        $this->assertSame('signing-key', $resolver->resolve(self::$certificate));
    }


    /**
     * Test that a malformed fingerprint (not 64 hex characters) throws InvalidArgumentException at construction.
     */
    public function testMalformedFingerprintThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FingerprintKeyNameResolver([
            'AB:CD:' . substr(self::CERT_FP, 6) => 'signing-key',
        ]);
    }


    /**
     * Test that two fingerprints differing only in case throw InvalidArgumentException at construction.
     */
    public function testCaseInsensitiveDuplicateFingerprintThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FingerprintKeyNameResolver([
            self::CERT_FP           => 'key-a',
            strtoupper(self::CERT_FP) => 'key-b',
        ]);
    }
}
