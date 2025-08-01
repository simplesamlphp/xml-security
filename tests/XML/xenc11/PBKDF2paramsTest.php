<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{AnyURIValue, PositiveIntegerValue, StringValue};
use SimpleSAML\XMLSecurity\XML\xenc11\{
    AbstractPBKDF2ParameterType,
    AbstractXenc11Element,
    IterationCount,
    KeyLength,
    OtherSource,
    Parameters,
    PBKDF2params,
    PRF,
    Salt,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\PBKDF2paramsTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(PBKDF2params::class)]
#[CoversClass(AbstractPBKDF2ParameterType::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class PBKDF2paramsTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = PBKDF2params::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_PBKDF2-params.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $someDoc = DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        );

        $parameters = new Parameters(
            [new Chunk($someDoc->documentElement)],
            [new XMLAttribute('urn:x-simplesamlphp:namespace', 'ssp', 'attr1', StringValue::fromString('testval1'))],
        );

        $otherSource = new OtherSource(
            AnyURIValue::fromString('urn:x-simplesamlphp:algorithm'),
            $parameters,
        );

        $salt = new Salt($otherSource);
        $iterationCount = new IterationCount(
            PositiveIntegerValue::fromString('3'),
        );
        $keyLength = new KeyLength(
            PositiveIntegerValue::fromString('4096'),
        );
        $prf = new PRF(
            AnyURIValue::fromString('urn:x-simplesamlphp:algorithm'),
        );

        $PBKDF2params = new PBKDF2params($salt, $iterationCount, $keyLength, $prf);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($PBKDF2params),
        );
    }
}
