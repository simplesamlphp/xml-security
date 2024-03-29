<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\Modulus;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ModulusTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(Modulus::class)]
final class ModulusTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Modulus::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Modulus.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $modulus = new Modulus('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg==');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($modulus),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(SchemaViolationException::class);
        new Modulus('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }
}
