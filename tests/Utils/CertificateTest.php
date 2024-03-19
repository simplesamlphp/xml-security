<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\Utils;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Utils\Certificate;

/**
 * @package simplesamlphp/xml-security
 */
#[Group('utilities')]
#[CoversClass(Certificate::class)]
final class CertificateTest extends TestCase
{
    #[Test]
    public function testValidStructure(): void
    {
        $result = Certificate::hasValidStructure(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::PUBLIC_KEY),
        );
        $this->assertTrue($result);

        $result = Certificate::hasValidStructure(
            PEMCertificatesMock::getPlainPublicKey(PEMCertificatesMock::BROKEN_PUBLIC_KEY),
        );
        $this->assertFalse($result);
    }


    #[Test]
    public function testConvertToCertificate(): void
    {
        $result = Certificate::convertToCertificate(PEMCertificatesMock::getPlainCertificateContents());
        // the formatted public key in PEMCertificatesMock is stored with unix newlines
        $this->assertEquals(trim(PEMCertificatesMock::getPlainCertificate()), $result);
    }


    #[Test]
    public function testParseIssuer(): void
    {
        // Test string input
        $result = Certificate::parseIssuer('test');
        $this->assertEquals('test', $result);

        // Test array input
        $result = Certificate::parseIssuer(
            [
                'C' => 'US',
                'S' => 'Hawaii',
                'L' => 'Honolulu',
                'O' => 'SimpleSAMLphp HQ',
                'CN' => 'SimpleSAMLphp Testing CA',
            ],
        );
        $this->assertEquals('CN=SimpleSAMLphp Testing CA,O=SimpleSAMLphp HQ,L=Honolulu,S=Hawaii,C=US', $result);
    }
}
