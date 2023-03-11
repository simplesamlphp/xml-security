<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XML\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\Exponent;
use SimpleSAML\XMLSecurity\XML\ds\Modulus;
use SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\RSAKeyValueTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue
 *
 * @package simplesamlphp/xml-security
 */
final class RSAKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = RSAKeyValue::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/ds_RSAKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $RSAKeyValue = new RSAKeyValue(
            new Modulus('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
            new Exponent('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo='),
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($RSAKeyValue),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $RSAKeyValue = new RSAKeyValue(
            new Modulus('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
            new Exponent('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo='),
        );

        $RSAKeyValueElement = $RSAKeyValue->toXML();
        $xpCache = XPath::getXPath($RSAKeyValueElement);

        $modulus = XPath::xpQuery($RSAKeyValueElement, './ds:Modulus', $xpCache);
        $this->assertCount(1, $modulus);

        /** @psalm-var \DOMElement[] $RSAKeyValueElements */
        $RSAKeyValueElements = XPath::xpQuery($RSAKeyValueElement, './ds:Modulus/following-sibling::*', $xpCache);

        // Test ordering of RSAKeyValue contents
        $this->assertCount(1, $RSAKeyValueElements);
        $this->assertEquals('ds:Exponent', $RSAKeyValueElements[0]->tagName);
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $RSAKeyValue = RSAKeyValue::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($RSAKeyValue),
        );
    }
}
