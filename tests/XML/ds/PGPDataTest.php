<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\Builtin\Base64BinaryValue;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, AbstractPGPDataType};
use SimpleSAML\XMLSecurity\XML\ds\{PGPData, PGPKeyID, PGPKeyPacket};
use SimpleSAML\XMLSecurity\XML\xenc\P;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\PGPDataTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractPGPDataType::class)]
#[CoversClass(PGPData::class)]
final class PGPDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    private static PGPKeyID $pgpKeyId;
    private static PGPKeyPacket $pgpKeyPacket;
    private static P $p;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = PGPData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_PGPData.xml',
        );

        self::$pgpKeyId = new PGPKeyID(
            Base64BinaryValue::fromString('GpM7'),
        );

        self::$pgpKeyPacket = new PGPKeyPacket(
            Base64BinaryValue::fromString('GpM8'),
        );

        self::$p = new P(
            CryptoBinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $pgpData = new PGPData(self::$pgpKeyId, self::$pgpKeyPacket, [self::$p]);

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
        $pgpData = new PGPData(self::$pgpKeyId, self::$pgpKeyPacket, [self::$p]);

        $pgpDataElement = $pgpData->toXML();
        /** @var \DOMElement[] $children */
        $children = $pgpDataElement->childNodes;

        $this->assertEquals('ds:PGPKeyID', $children[0]->tagName);
        $this->assertEquals('ds:PGPKeyPacket', $children[1]->tagName);
        $this->assertEquals('xenc:P', $children[2]->tagName);
    }
}
