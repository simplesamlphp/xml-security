<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SchemaValidationTestTrait;
use SimpleSAML\Test\XML\SerializableElementTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\KeyInfoReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element
 * @covers \SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfoReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = KeyInfoReference::class;

        $this->schema = dirname(__FILE__, 4) . '/schemas/xmldsig11-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_KeyInfoReference.xml',
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

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($KeyInfoReference),
        );
    }
}
