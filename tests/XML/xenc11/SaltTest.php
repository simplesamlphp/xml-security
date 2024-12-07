<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\xenc11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element;
use SimpleSAML\XMLSecurity\XML\xenc11\OtherSource;
use SimpleSAML\XMLSecurity\XML\xenc11\Parameters;
use SimpleSAML\XMLSecurity\XML\xenc11\Salt;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\xenc11\SaltTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Salt::class)]
#[CoversClass(AbstractXenc11Element::class)]
final class SaltTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Salt::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_Salt.xml',
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

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($salt),
        );
    }
}
