<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\SignatureMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureMethodTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;

    /**
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_SignatureMethod.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $SignatureMethod = new SignatureMethod(Constants::SIG_RSA_SHA256);

        $this->assertEquals(Constants::SIG_RSA_SHA256, $SignatureMethod->getAlgorithm());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($SignatureMethod));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $SignatureMethod = SignatureMethod::fromXML($this->document->documentElement);

        $this->assertEquals(Constants::SIG_RSA_SHA256, $SignatureMethod->getAlgorithm());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(SignatureMethod::fromXML($this->document->documentElement))))
        );
    }
}
