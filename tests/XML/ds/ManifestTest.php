<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SchemaValidationTestTrait;
use SimpleSAML\Test\XML\SerializableElementTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;
use SimpleSAML\XMLSecurity\XML\ds\Manifest;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ManifestTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Manifest
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/saml2
 */
final class ManifestTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Manifest::class;

        $this->schema = dirname(dirname(dirname(dirname(__FILE__)))) . '/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_Manifest.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $reference = new Reference(
            new DigestMethod(C::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            new Transforms(
                [
                    new Transform(C::XMLDSIG_ENVELOPED),
                    new Transform(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
                ],
            ),
            'abc123',
            C::XMLDSIG_MANIFEST,
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153',
        );
        $manifest = new Manifest([$reference], 'abc123');

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($manifest),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $manifest = Manifest::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($manifest),
        );
    }
}
