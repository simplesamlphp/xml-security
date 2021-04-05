<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod
 *
 * @package simplesamlphp/xml-security
 */
final class CanonicalizationMethodTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = CanonicalizationMethod::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_CanonicalizationMethod.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $canonicalizationMethod = new CanonicalizationMethod(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS);

        $canonicalizationMethodElement = $canonicalizationMethod->toXML();
        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $canonicalizationMethodElement->getAttribute('Algorithm'));

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($canonicalizationMethod)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $CanonicalizationMethod = CanonicalizationMethod::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $CanonicalizationMethod->getAlgorithm());
    }
}
