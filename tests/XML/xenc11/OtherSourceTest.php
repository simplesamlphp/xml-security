<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractAlgorithmIdentifierType;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element;
use SimpleSAML\XMLSecurity\XML\xenc11\OtherSource;
use SimpleSAML\XMLSecurity\XML\xenc11\Parameters;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\OtherSourceTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(OtherSource::class)]
#[CoversClass(AbstractAlgorithmIdentifierType::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class OtherSourceTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = OtherSource::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_OtherSource.xml',
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

        $otherSource = new OtherSource(
            AnyURIValue::fromString('urn:x-simplesamlphp:algorithm'),
            $parameters,
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($otherSource),
        );
    }
}
