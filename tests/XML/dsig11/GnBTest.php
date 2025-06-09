<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractCharTwoFieldParamsType;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\GnB;
use SimpleSAML\XMLSecurity\XML\dsig11\M;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\GnBTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractCharTwoFieldParamsType::class)]
#[CoversClass(M::class)]
#[CoversClass(GnB::class)]
final class GnBTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = GnB::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_GnB.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $m = new M(1024);
        $gnb = new GnB($m);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($gnb),
        );
    }
}
