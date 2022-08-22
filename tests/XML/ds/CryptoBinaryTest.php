<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\CryptoBinary;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\CryptoBinaryTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\CryptoBinary
 *
 * @package simplesamlphp/xml-security
 */
final class CryptoBinaryTest extends TestCase
{
    use SerializableXMLTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = CryptoBinary::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_CryptoBinary.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $CryptoBinary = new CryptoBinary('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content($this->xmlRepresentation),
            strval($CryptoBinary),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(AssertionFailedException::class);
        new CryptoBinary('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $CryptoBinary = CryptoBinary::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=', $CryptoBinary->getContent());
    }
}
