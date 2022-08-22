<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\XML\ds\Exponent;
use SimpleSAML\XMLSecurity\XML\ds\KeyValue;
use SimpleSAML\XMLSecurity\XML\ds\Modulus;
use SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\KeyValueTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\KeyValue
 *
 * @package simplesamlphp/xml-security
 */
final class KeyValueTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = KeyValue::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_KeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyValue = new KeyValue(
            new RSAKeyValue(
                new Modulus('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
                new Exponent('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo='),
            ),
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($keyValue),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyValue = KeyValue::fromXML($this->xmlRepresentation->documentElement);

        $RSAKeyValue = $keyValue->getRSAKeyValue();
        $this->assertNotNull($RSAKeyValue);
        $this->assertEmpty($keyValue->getElements());

        $this->assertEquals('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg==', $RSAKeyValue->getModulus()->getContent());
        $this->assertEquals('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo=', $RSAKeyValue->getExponent()->getContent());
    }
}
