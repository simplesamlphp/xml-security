<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\DigestValueTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\DigestValue
 *
 * @package simplesamlphp/xml-security
 */
final class DigestValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = DigestValue::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_DigestValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $digestValue = new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content($this->xmlRepresentation),
            strval($digestValue),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(AssertionFailedException::class);
        new DigestValue('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $digestValue = DigestValue::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($digestValue),
        );
    }
}
