<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSecurity\XML\xenc11\{
    AbstractAlgorithmIdentifierType,
    AbstractPRFAlgorithmIdentifierType,
    AbstractXenc11Element,
    PRF,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\PRFTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(PRF::class)]
#[CoversClass(AbstractPRFAlgorithmIdentifierType::class)]
#[CoversClass(AbstractAlgorithmIdentifierType::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class PRFTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = PRF::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_PRF.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $prf = new PRF(
            AnyURIValue::fromString('urn:x-simplesamlphp:algorithm'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($prf),
        );
    }
}
