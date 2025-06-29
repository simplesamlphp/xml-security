<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\Builtin\StringValue;
use SimpleSAML\XMLSecurity\XML\xenc11\{AbstractXenc11Element, Parameters};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\ParametersTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(Parameters::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class ParametersTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Parameters::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_Parameters.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $chunk = new Chunk(DOMDocumentFactory::fromString(
            <<<XML
  <ssp:AuthenticationContextDeclaration xmlns:ssp="urn:x-simplesamlphp:namespace">
    <ssp:Identification nym="verinymity">
      <ssp:Extension>
        <ssp:NoVerification/>
      </ssp:Extension>
    </ssp:Identification>
  </ssp:AuthenticationContextDeclaration>
XML
            ,
        )->documentElement);

        $parameters = new Parameters(
            [$chunk],
            [new XMLAttribute('urn:x-simplesamlphp:namespace', 'ssp', 'attr1', StringValue::fromString('testval1'))],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($parameters),
        );
    }


    /**
     * Adding an empty Parameters element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $xenc11_ns = Parameters::NS;
        $parameters = new Parameters();
        $this->assertEquals(
            "<xenc11:Parameters xmlns:xenc11=\"$xenc11_ns\"/>",
            strval($parameters),
        );
        $this->assertTrue($parameters->isEmptyElement());
    }
}
