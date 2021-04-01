<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\X509Digest;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509DigestTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509Digest
 *
 * @package simplesamlphp/xml-security
 */
final class X509DigestTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;

    /** @var \SimpleSAML\XMLSecurity\Key\X509Certificate */
    private Key\X509Certificate $key;

    /** @var string */
    private string $digest;


    /**
     */
    public function setUp(): void
    {
        $this->key = new Key\X509Certificate(
            PEMCertificatesMock::getPlainPublicKey()
        );
        $this->digest = base64_encode(hex2bin($this->key->getRawThumbprint(Constants::DIGEST_SHA256)));

        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509Digest.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $X509digest = new X509Digest($this->digest, Constants::DIGEST_SHA256);

        $this->assertEquals($this->digest, $X509digest->getDigest());
        $this->assertEquals(Constants::DIGEST_SHA256, $X509digest->getAlgorithm());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($X509digest));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $X509digest = X509Digest::fromXML($this->document->documentElement);

        $this->assertEquals($this->digest, $X509digest->getDigest());
        $this->assertEquals(Constants::DIGEST_SHA256, $X509digest->getAlgorithm());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(X509Digest::fromXML($this->document->documentElement))))
        );
    }
}
