<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\ReferenceTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\Reference
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/saml2
 */
final class ReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Reference::class;

        $this->schema = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/ds_Reference.xml',
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
            'ghi789',
            'urn:some:type',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153',
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($reference),
        );
    }


    /**
     */
    public function testMarshallingReferenceElementOrdering(): void
    {
        $reference = new Reference(
            new DigestMethod(C::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            new Transforms(
                [
                    new Transform(C::XMLDSIG_ENVELOPED),
                    new Transform(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
                ]
            ),
            'ghi789',
            'urn:some:type',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153',
        );

        $referenceElement = $reference->toXML();
        $children = $referenceElement->childNodes;

        $this->assertEquals('ds:Transforms', $children[0]->tagName);
        $this->assertEquals('ds:DigestMethod', $children[1]->tagName);
        $this->assertEquals('ds:DigestValue', $children[2]->tagName);
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $reference = Reference::fromXML($this->xmlRepresentation->documentElement);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($reference),
        );
    }
}
