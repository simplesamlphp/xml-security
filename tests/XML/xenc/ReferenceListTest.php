<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\ds\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\KeyReference;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\ReferenceListTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList
 *
 * @package simplesamlphp/xml-security
 */
final class ReferenceListTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public function setup(): void
    {
        $this->testedClass = ReferenceList::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/xenc_ReferenceList.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $transformData = new Transform(
            C::XPATH_URI,
            new XPath('self::xenc:EncryptedData[@Id="example1"]'),
        );
        $transformKey = new Transform(
            C::XPATH_URI,
            new XPath('self::xenc:EncryptedKey[@Id="example1"]'),
        );

        $referenceList = new ReferenceList(
            [
                new DataReference('#Encrypted_DATA_ID', [new Transforms([$transformData])])
            ],
            [
                new KeyReference('#Encrypted_KEY_ID', [new Transforms([$transformKey])])
            ],
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($referenceList),
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $referenceList = ReferenceList::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($referenceList),
        );
    }
}
