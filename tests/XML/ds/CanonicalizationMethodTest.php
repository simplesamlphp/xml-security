<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
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
    protected function setUp(): void
    {
        $this->testedClass = CanonicalizationMethod::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_CanonicalizationMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $canonicalizationMethod = new CanonicalizationMethod(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($canonicalizationMethod),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $canonicalizationMethod = CanonicalizationMethod::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $canonicalizationMethod->getAlgorithm());
    }
}
