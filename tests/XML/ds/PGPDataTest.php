<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\AbstractPGPDataType;
use SimpleSAML\XMLSecurity\XML\ds\PGPData;
use SimpleSAML\XMLSecurity\XML\ds\PGPKeyID;
use SimpleSAML\XMLSecurity\XML\ds\PGPKeyPacket;
use SimpleSAML\XMLSecurity\XML\xenc\P;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\PGPDataTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractPGPDataType::class)]
#[CoversClass(PGPData::class)]
final class PGPDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = PGPData::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_PGPData.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $pgpKeyId = new PGPKeyID('GpM7');
        $pgpKeyPacket = new PGPKeyPacket('GpM8');
        $p = new P('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $pgpData = new PGPData($pgpKeyId, $pgpKeyPacket, [$p]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($pgpData),
        );
    }


    /**
     */
    public function testMarshallingBothIdAndPacketNullThrowsException(): void
    {
        $this->expectException(SchemaViolationException::class);

        new PGPData(null, null, []);
    }


    /**
     */
    public function testMarshallingReferenceElementOrdering(): void
    {
        $pgpKeyId = new PGPKeyID('GpM7');
        $pgpKeyPacket = new PGPKeyPacket('GpM8');
        $p = new P('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $pgpData = new PGPData($pgpKeyId, $pgpKeyPacket, [$p]);

        $pgpDataElement = $pgpData->toXML();
        /** @var \DOMElement[] $children */
        $children = $pgpDataElement->childNodes;

        $this->assertEquals('ds:PGPKeyID', $children[0]->tagName);
        $this->assertEquals('ds:PGPKeyPacket', $children[1]->tagName);
        $this->assertEquals('xenc:P', $children[2]->tagName);
    }
}
