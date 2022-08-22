<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfoReference;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\KeyInfoReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\KeyInfoReference
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfoReferenceTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = KeyInfoReference::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_KeyInfoReference.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $KeyInfoReference = new KeyInfoReference('#_e395489e5f8444f1aabb4b2ca98a23b793d211ddf0', 'abc123');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($KeyInfoReference),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $KeyInfoReference = KeyInfoReference::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('#_e395489e5f8444f1aabb4b2ca98a23b793d211ddf0', $KeyInfoReference->getURI());
        $this->assertEquals('abc123', $KeyInfoReference->getId());
    }
}
