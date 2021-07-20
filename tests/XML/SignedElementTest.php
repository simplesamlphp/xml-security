<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Key\PrivateKey;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\SignedElementTest
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

    /** @var string */
    private string $certificate;

    /** @var PrivateKey */
    private PrivateKey $key;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = CustomSignable::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSigned.xml'
        );

        $certificate = file_get_contents(
            dirname(dirname(__FILE__)) . '/resources/certificates/rsa-pem/selfsigned.simplesamlphp.org.crt'
        );
        $certificateLines = explode("\n", trim($certificate));
        array_pop($certificateLines);
        array_shift($certificateLines);
        $this->certificate = join("\n", $certificateLines);

        $this->key = PrivateKey::fromFile(
            dirname(dirname(__FILE__)) . '/resources/certificates/rsa-pem/selfsigned.simplesamlphp.org_nopasswd.key'
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $customSigned = CustomSignable::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($customSigned)
        );
   }
}

