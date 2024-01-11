<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod;

use function dirname;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\CanonicalizationMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod
 *
 * @package simplesamlphp/xml-security
 */
final class CanonicalizationMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = CanonicalizationMethod::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_CanonicalizationMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $canonicalizationMethod = new CanonicalizationMethod(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($canonicalizationMethod),
        );
    }
}
