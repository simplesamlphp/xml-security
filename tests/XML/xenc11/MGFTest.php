<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\AnyURIValue;
use SimpleSAML\XMLSecurity\XML\xenc11\{
    AbstractAlgorithmIdentifierType,
    AbstractMGFType,
    AbstractXenc11Element,
    MGF,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\MGFTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(MGF::class)]
#[CoversClass(AbstractMGFType::class)]
#[CoversClass(AbstractAlgorithmIdentifierType::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class MGFTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = MGF::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_MGF.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $mgf = new MGF(
            AnyURIValue::fromString('urn:x-simplesamlphp:algorithm'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($mgf),
        );
    }
}
