<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
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
    /** @var \DOMDocument */
    private DOMDocument $document;

    /**
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_CanonicalizationMethod.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $CanonicalizationMethod = new CanonicalizationMethod(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS);

        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $CanonicalizationMethod->getAlgorithm());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($CanonicalizationMethod));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $CanonicalizationMethod = CanonicalizationMethod::fromXML($this->document->documentElement);

        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $CanonicalizationMethod->getAlgorithm());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(CanonicalizationMethod::fromXML($this->document->documentElement))))
        );
    }
}
