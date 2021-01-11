<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509SubjectNameTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509SubjectName
 *
 * @package simplesamlphp/xml-security
 */
final class X509SubjectNameTest extends TestCase
{
    /** @var \DOMDocument */
    private DOMDocument $document;


    /**
     */
    protected function setUp(): void
    {
        $this->document = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509SubjectName.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $subjectName = new X509SubjectName('some name');

        $this->assertEquals('some name', $subjectName->getName());

        $this->assertEquals($this->document->saveXML($this->document->documentElement), strval($subjectName));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $subjectName = X509SubjectName::fromXML($this->document->documentElement);

        $this->assertEquals('some name', $subjectName->getName());
    }


    /**
     * Test serialization / unserialization
     */
    public function testSerialization(): void
    {
        $this->assertEquals(
            $this->document->saveXML($this->document->documentElement),
            strval(unserialize(serialize(X509SubjectName::fromXML($this->document->documentElement))))
        );
    }
}
