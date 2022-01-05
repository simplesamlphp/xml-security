<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\Test\ds\DigestMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\DigestMethod
 *
 * @package simplesamlphp/xml-security
 */
final class DigestMethodTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = DigestMethod::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_DigestMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $digestMethod = new DigestMethod(
            C::DIGEST_SHA256,
            [new Chunk(DOMDocumentFactory::fromString('<some:Chunk>Random</some:Chunk>')->documentElement)],
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($digestMethod),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $digestMethod = DigestMethod::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(C::DIGEST_SHA256, $digestMethod->getAlgorithm());
    }
}
