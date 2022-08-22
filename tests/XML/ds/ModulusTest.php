<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\Modulus;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ModulusTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Modulus
 *
 * @package simplesamlphp/xml-security
 */
final class ModulusTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = Modulus::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_Modulus.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $modulus = new Modulus('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg==');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content($this->xmlRepresentation),
            strval($modulus),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(SchemaViolationException::class);
        new Modulus('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $modulus = Modulus::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg==', $modulus->getContent());
    }
}
