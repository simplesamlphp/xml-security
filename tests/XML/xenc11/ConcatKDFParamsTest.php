<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\{AnyURIValue, HexBinaryValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\xenc11\{AbstractConcatKDFParamsType, AbstractXenc11Element, ConcatKDFParams};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\xenc11\ConcatKDFParamsTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(AbstractConcatKDFParamsType::class)]
#[CoversClass(ConcatKDFParams::class)]
final class ConcatKDFParamsTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = ConcatKDFParams::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_ConcatKDFParams.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $digestMethod = new DigestMethod(
            AnyURIValue::fromString(C::DIGEST_SHA256),
            [
                new Chunk(DOMDocumentFactory::fromString(
                    '<some:Chunk xmlns:some="urn:test:some">Random</some:Chunk>',
                )->documentElement),
            ],
        );

        $concatKdfParams = new ConcatKDFParams(
            $digestMethod,
            HexBinaryValue::fromString('a1b2'),
            HexBinaryValue::fromString('b2c3'),
            HexBinaryValue::fromString('c3d4'),
            HexBinaryValue::fromString('d4e5'),
            HexBinaryValue::fromString('e5f6'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($concatKdfParams),
        );
    }
}
