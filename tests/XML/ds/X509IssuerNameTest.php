<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\X509IssuerName;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509IssuerNameTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\X509IssuerName
 *
 * @package simplesamlphp/xml-security
 */
final class X509IssuerNameTest extends TestCase
{
    use SerializableElementTestTrait;

    /**
     */
    protected function setUp(): void
    {
        $this->testedClass = X509IssuerName::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509IssuerName.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $issuerName = new X509IssuerName('some name');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($issuerName),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $issuerName = X509IssuerName::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($issuerName),
        );
    }
}
