<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

/**
 * Class \SAML2\XML\ds\ReferenceTest
 *
 * @covers \SimpleSAML\SAML2\XML\ds\Reference
 * @covers \SimpleSAML\SAML2\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/saml2
 */
final class ReferenceTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = Reference::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_Reference.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $reference = new Reference(
            new DigestMethod(Constants::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            new Transforms(
                [
                    new Transform(Constants::XMLDSIG_ENVELOPED),
                    new Transform(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS)
                ]
            ),
            'abc123',
            'someType',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($reference)
        );
    }


    /**
     */
    public function testMarshallingReferenceElementOrdering(): void
    {
        $reference = new Reference(
            new DigestMethod(Constants::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            new Transforms(
                [
                    new Transform(Constants::XMLDSIG_ENVELOPED),
                    new Transform(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS)
                ]
            ),
            'abc123',
            'someType',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'
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
        $this->assertEquals('abc123', $reference->getId());
        $this->assertEquals('someType', $reference->getType());
        $this->assertEquals('#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153', $reference->getURI());

        $digestMethod = $reference->getDigestMethod();
        $this->assertEquals(Constants::DIGEST_SHA256, $digestMethod->getAlgorithm());

        $digestValue = $reference->getDigestValue();
        $this->assertEquals('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=', $digestValue->getDigest());


        $transforms = $reference->getTransforms();
        $transform = $transforms->getTransform();
        $this->assertCount(2, $transform);

        $this->assertEquals(Constants::XMLDSIG_ENVELOPED, $transform[0]->getAlgorithm());
        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $transform[1]->getAlgorithm());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($reference)
        );
    }
}
