<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureMethodTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = SignatureMethod::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_SignatureMethod.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $signatureMethod = new SignatureMethod(Constants::SIG_RSA_SHA256);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signatureMethod)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $signatureMethod = SignatureMethod::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(Constants::SIG_RSA_SHA256, $signatureMethod->getAlgorithm());
    }
}
