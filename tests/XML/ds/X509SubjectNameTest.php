<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTest;
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
final class X509SubjectNameTest extends SerializableXMLTest
{
    /**
     */
    protected function setUp(): void
    {
        self::$element = X509SubjectName::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_X509SubjectName.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $subjectName = new X509SubjectName('some name');

        $this->assertEquals('some name', $subjectName->getName());

        $this->assertEquals(self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement), strval($subjectName));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $subjectName = X509SubjectName::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals('some name', $subjectName->getName());
    }
}
