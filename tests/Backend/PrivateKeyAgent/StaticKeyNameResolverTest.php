<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Backend\PrivateKeyAgent;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\StaticKeyNameResolver;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\X509Certificate;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * Tests for StaticKeyNameResolver.
 *
 * @package simplesamlphp/xml-security
 */
final class StaticKeyNameResolverTest extends TestCase
{
    private static X509Certificate $certificate;


    public static function setUpBeforeClass(): void
    {
        // Only a certificate (public side), no private key material used in fixtures.
        self::$certificate = PEMCertificatesMock::getCertificate(PEMCertificatesMock::CERTIFICATE);
    }


    /**
     * Test that the resolver returns the configured name for any certificate.
     */
    public function testResolveReturnsConfiguredName(): void
    {
        $resolver = new StaticKeyNameResolver('my-signing-key');
        $this->assertSame('my-signing-key', $resolver->resolve(self::$certificate));
    }


    /**
     * Test that valid edge-case names are accepted by verifying the resolver returns them.
     */
    public function testValidKeyNameEdgeCases(): void
    {
        $this->assertSame('a', (new StaticKeyNameResolver('a'))->resolve(self::$certificate));
        $this->assertSame('A1-_', (new StaticKeyNameResolver('A1-_'))->resolve(self::$certificate));
        $longName = str_repeat('x', 64);
        $this->assertSame($longName, (new StaticKeyNameResolver($longName))->resolve(self::$certificate));
    }


    /**
     * Test that an empty key name fails at construction.
     */
    public function testEmptyKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticKeyNameResolver('');
    }


    /**
     * Test that a key name exceeding 64 characters fails at construction.
     */
    public function testTooLongKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticKeyNameResolver(str_repeat('x', 65));
    }


    /**
     * Test that a key name containing invalid characters fails at construction.
     */
    public function testInvalidCharsKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticKeyNameResolver('invalid/name');
    }


    /**
     * Test that a key name with spaces fails at construction.
     */
    public function testSpacesInKeyNameThrowsAtConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticKeyNameResolver('has space');
    }
}
