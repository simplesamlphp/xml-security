<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;

use function dirname;
use function str_replace;
use function strval;
use function substr;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\X509CertificateTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(X509Certificate::class)]
final class X509CertificateTest extends TestCase
{
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = X509Certificate::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509Certificate.xml',
        );

        self::$certificate = str_replace(
            [
                '-----BEGIN CERTIFICATE-----',
                '-----END CERTIFICATE-----',
                '-----BEGIN RSA PUBLIC KEY-----',
                '-----END RSA PUBLIC KEY-----',
                "\r\n",
                "\n",
            ],
            [
                '',
                '',
                '',
                '',
                "\n",
                '',
            ],
            PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::SELFSIGNED_CERTIFICATE),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $x509cert = new X509Certificate(self::$certificate);

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($x509cert),
        );
    }


    /**
     */
    public function testMarshallingInvalidBase64(): void
    {
        $certificate = str_replace(substr(self::$certificate, 1), '', self::$certificate);
        $this->expectException(AssertionFailedException::class);
        new X509Certificate($certificate);
    }
}
