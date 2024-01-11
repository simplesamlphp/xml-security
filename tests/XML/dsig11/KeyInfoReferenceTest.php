<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
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
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = KeyInfoReference::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig11-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_KeyInfoReference.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $KeyInfoReference = new KeyInfoReference('#_e395489e5f8444f1aabb4b2ca98a23b793d211ddf0', 'abc123');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($KeyInfoReference),
        );
    }
}
