<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
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
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = CustomSigned::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSigned.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<ssp:Some>Chunk</ssp:Some>'
        );

        $customSignable = new CustomSignable($document->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $privateKey = PEMCertificatesMock::getPrivateKey(XMLSecurityKey::RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
        $customSigned = $customSignable->sign($privateKey);

        $this->assertEqualXMLStructure(
            $this->xmlRepresentation->documentElement,
            $customSigned->toXML()->ownerDocument->documentElement
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $customSignable = CustomSignable::fromXML($this->xmlRepresentation->documentElement);

        $customSignableElement = $customSignable->getElement();

        $this->assertEqualXMLStructure($this->xmlRepresentation->documentElement, $customSignableElement->ownerDocument->documentElement);
   }
}

