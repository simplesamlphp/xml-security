<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ec;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ec\InclusiveNamespacesTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces
 *
 * @package simplesamlphp/xml-security
 */
class InclusiveNamespacesTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = InclusiveNamespaces::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ec_InclusiveNamespaces.xml'
        );
    }


    public function testMarshalling(): void
    {
        $inclusiveNamespaces = new InclusiveNamespaces(["dsig", "soap", "#default"]);

        $this->assertCount(3, $inclusiveNamespaces->getPrefixes());
        $this->assertEquals("dsig", $inclusiveNamespaces->getPrefixes()[0]);
        $this->assertEquals("soap", $inclusiveNamespaces->getPrefixes()[1]);
        $this->assertEquals("#default", $inclusiveNamespaces->getPrefixes()[2]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($inclusiveNamespaces)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $inclusiveNamespaces = InclusiveNamespaces::fromXML(
            $this->xmlRepresentation->documentElement
        );
        $prefixes = $inclusiveNamespaces->getPrefixes();
        $this->assertCount(3, $prefixes);

        $this->assertEquals('dsig', $prefixes[0]);
        $this->assertEquals('soap', $prefixes[1]);
        $this->assertEquals('#default', $prefixes[2]);


        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($inclusiveNamespaces)
        );
    }
}
