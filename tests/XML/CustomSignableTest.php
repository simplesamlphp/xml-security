<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use DOMElement;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSecurity\TestUtils\SignedElementTestTrait;
use SimpleSAML\Test\XML\SerializableElementTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;

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


    /** @var \DOMElement */
    private DOMElement $logoutRequestElement;


    /**
     * Load a fixture.
     */
    public function setUp(): void
    {
        $this->testedClass = CustomSignable::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignable.xml',
        );
    }


    /**
     * No marshalling test, because the constructor is private
     */


    /**
     */
    public function testUnmarshalling(): void
    {
        $customSignable = CustomSignable::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($customSignable),
        );
    }
}