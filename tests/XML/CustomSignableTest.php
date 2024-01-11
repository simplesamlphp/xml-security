<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\TestUtils\SignedElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\CustomSignableTest
 *
 * @covers \SimpleSAML\XMLSecurity\Test\XML\CustomSignable
 * @package simplesamlphp/xml-security
 */
final class CustomSignableTest extends TestCase
{
    use SerializableElementTestTrait;
    use SignedElementTestTrait;


    /**
     * Load a fixture.
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = CustomSignable::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignable.xml',
        );
    }


    /**
     * No marshalling test, because the constructor is private
     */


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(<<<XML
<ssp:CustomSignable xmlns:ssp="urn:x-simplesamlphp:namespace"><ssp:Chunk>Some</ssp:Chunk></ssp:CustomSignable>
XML
        );

        $customSignable = new CustomSignable($document->documentElement, null);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($customSignable),
        );
    }
}
