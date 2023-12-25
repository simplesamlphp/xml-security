<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\XPath;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\XPathTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\XPath
 *
 * @package simplesamlphp/xml-security
 */
class XPathTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = XPath::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_XPath.xml',
        );
    }


    public function testMarshalling(): void
    {
        $xpath = new XPath(
            'self::xenc:CipherValue[@Id="example1"]',
            [
                'xenc' => 'http://www.w3.org/2001/04/xmlenc#',
            ],
        );

        $this->assertEquals('self::xenc:CipherValue[@Id="example1"]', $xpath->getExpression());
        $namespaces = $xpath->getNamespaces();
        $this->assertCount(1, $namespaces);
        $this->assertArrayHasKey('xenc', $namespaces);
        $this->assertEquals('http://www.w3.org/2001/04/xmlenc#', $namespaces['xenc']);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($xpath),
        );
    }
}
