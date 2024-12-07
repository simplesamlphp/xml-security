<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractPBKDF2ParameterType;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element;
use SimpleSAML\XMLSecurity\XML\xenc11\IterationCount;
use SimpleSAML\XMLSecurity\XML\xenc11\KeyLength;
use SimpleSAML\XMLSecurity\XML\xenc11\OtherSource;
use SimpleSAML\XMLSecurity\XML\xenc11\Parameters;
use SimpleSAML\XMLSecurity\XML\xenc11\PBKDF2params;
use SimpleSAML\XMLSecurity\XML\xenc11\PRF;
use SimpleSAML\XMLSecurity\XML\xenc11\Salt;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\PBKDF2paramsTest
 *
 * @package simplesamlphp/xml-security
 */
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

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema-11.xsd';

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
            [new XMLAttribute('urn:x-simplesamlphp:namespace', 'ssp', 'attr1', 'testval1')],
        );

        $otherSource = new OtherSource('urn:x-simplesamlphp:algorithm', $parameters);

        $salt = new Salt($otherSource);
        $iterationCount = new IterationCount(3);
        $keyLength = new KeyLength(4096);
        $prf = new PRF('urn:x-simplesamlphp:algorithm');

        $PBKDF2params = new PBKDF2params($salt, $iterationCount, $keyLength, $prf);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($PBKDF2params),
        );
    }
}
