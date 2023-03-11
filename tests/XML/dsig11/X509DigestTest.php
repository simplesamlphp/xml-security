<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\dsig11\X509Digest;

use function base64_encode;
use function dirname;
use function hex2bin;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\X509DigestTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element
 * @covers \SimpleSAML\XMLSecurity\XML\dsig11\X509Digest
 *
 * @package simplesamlphp/xml-security
 */
final class X509DigestTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    private Key\X509Certificate $key;

    /** @var string */
    private string $digest;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = X509Digest::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig11-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/dsig11_X509Digest.xml',
        );
        $this->key = new Key\X509Certificate(PEM::fromString(PEMCertificatesMock::getPlainCertificate()));

        $this->digest = base64_encode(hex2bin($this->key->getRawThumbprint(C::DIGEST_SHA256)));
    }


    /**
     */
    public function testMarshalling(): void
    {
        $x509digest = new X509Digest($this->digest, C::DIGEST_SHA256);

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content($this->xmlRepresentation),
            strval($x509digest),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $x509digest = X509Digest::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals($x509digest->getContent(), $this->digest);
        $this->assertEquals(C::DIGEST_SHA256, $x509digest->getAlgorithm());
    }
}
