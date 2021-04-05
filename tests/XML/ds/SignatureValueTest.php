<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\SignatureValue;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\SignatureValue
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\\XML\ds\SignatureValue
 *
 * @package simplesamlphp/xml-security
 */
class SignatureValueTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->testedClass = SignatureValue::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_SignatureValue.xml'
        );
    }


    /**
     * Test creating a SignatureValue from scratch.
     */
    public function testMarshalling(): void
    {
        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval(new SignatureValue('kdutrEsAEw56Sefgs34...'))
        );
    }


    /**
     * Test creating a SignatureValue object from XML.
     */
    public function testUnmarshalling(): void
    {
        $signatureValue = SignatureValue::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('kdutrEsAEw56Sefgs34...', $signatureValue->getValue());
    }
}