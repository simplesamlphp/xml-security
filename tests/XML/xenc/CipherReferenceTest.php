<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\CipherReference;
use SimpleSAML\XMLSecurity\XML\xenc\Transforms;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\CipherReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\AbstractReference
 * @covers \SimpleSAML\XMLSecurity\XML\xenc\CipherReference
 *
 * @package simplesamlphp/xml-security
 */
final class CipherReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\XMLSecurity\XML\xenc\Transforms $transforms */
    private Transforms $transforms;


    /**
     */
    public function setup(): void
    {
        $this->testedClass = CipherReference::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/xenc_CipherReference.xml',
        );

        $xpath = new XPath('count(//. | //@* | //namespace::*)');
        $transform = new Transform(C::XPATH_URI, $xpath);
        $this->transforms = new Transforms([$transform]);
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $cipherReference = new CipherReference('#Cipher_VALUE_ID', [$this->transforms]);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherReference),
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshalling(): void
    {
        $cipherReference = CipherReference::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($cipherReference),
        );
    }
}
