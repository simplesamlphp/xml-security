<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, Modulus};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ModulusTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
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
        $modulus = new Modulus(
            CryptoBinaryValue::fromString('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($modulus),
        );
    }
}
