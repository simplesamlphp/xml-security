<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\{Base64BinaryValue, StringValue};
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, AbstractSPKIDataType};
use SimpleSAML\XMLSecurity\XML\ds\{SPKIData, SPKISexp};
use SimpleSAML\XMLSecurity\XML\xenc\{CarriedKeyName, Seed};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SPKIDataTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(AbstractSPKIDataType::class)]
#[CoversClass(SPKIData::class)]
final class SPKIDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SPKIData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SPKIData.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $SPKISexp1 = new SPKISexp(
            Base64BinaryValue::fromString('GpM6'),
        );
        $seed = new Seed(
            CryptoBinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
        );
        $SPKISexp2 = new SPKISexp(
            Base64BinaryValue::fromString('GpM7'),
        );
        $SPKISexp3 = new SPKISexp(
            Base64BinaryValue::fromString('GpM8'),
        );
        $carriedKeyName = new CarriedKeyName(
            StringValue::fromString('Some label'),
        );
        $SPKISexp4 = new SPKISexp(
            Base64BinaryValue::fromString('GpM9'),
        );

        $SPKIData = new SPKIData([
            [$SPKISexp1, $seed],
            [$SPKISexp2, null],
            [$SPKISexp3, $carriedKeyName],
            [$SPKISexp4, null],
        ]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($SPKIData),
        );
    }
}
