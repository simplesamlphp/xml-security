<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSecurity\Test\XML\CustomSigned;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Class \SimpleSAML\XMLSecurity\XML\CustomSignedTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\AbstractSignedXMLElement
 * @covers \SimpleSAML\XMLSecurity\XML\SignedElementTrait
 * @covers \SimpleSAML\XMLSecurity\Test\XML\CustomSigned
 *
 * @package simplesamlphp/xml-security
 */
final class SignedElementTest extends TestCase
{
    /**
     */
    public function setUp(): void
    {
        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSigned.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<some>Chunk</some>'
        );

        $customSignable = new CustomSignable(new Chunk($document->documentElement));
        $this->assertFalse($customSignable->isEmptyElement());

        $privateKey = PEMCertificatesMock::getPrivateKey(XMLSecurityKey::RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
        $customSigned = $customSignable->sign($privateKey);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($customSigned)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $customSignable = CustomSignable::fromXML($this->xmlRepresentation->documentElement);

        $customSignableElement = $customSignable->getElement();
        $customSignableElement = $customSignableElement->getXML();

        $this->assertEquals('some', $customSignableElement->tagName);
        $this->assertEquals(
            'Chunk',
            $customSignableElement->textContent
        );
   }
}

