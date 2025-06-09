<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\Base;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\BaseTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(Base::class)]
final class BaseTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Base::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_Base.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $base = new Base('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($base),
        );
    }
}
