<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

/**
 * @covers \SimpleSAML\XMLSecurity\Utils\Certificate
 * @package simplesamlphp/saml2
 */
final class CertificateTest extends TestCase
{
    /**
     * @group utilities
     * @test
     */
    public function testValidStructure(): void
    {
        $result = Certificate::hasValidStructure(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::PUBLIC_KEY)
        );
        $this->assertTrue($result);
        $result = Certificate::hasValidStructure(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::BROKEN_PUBLIC_KEY)
        );
        $this->assertFalse($result);
    }


    /**
     * @group utilities
     * @test
     */
    public function testConvertToCertificate(): void
    {
        $result = Certificate::convertToCertificate(PEMCertificatesMock::getPlainPublicKeyContents());
        // the formatted public key in PEMCertificatesMock is stored with unix newlines
        $this->assertEquals(trim(PEMCertificatesMock::getPlainPublicKey()), $result);
    }
}

