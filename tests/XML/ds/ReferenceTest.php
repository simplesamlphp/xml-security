<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\Constants;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
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
            new Transforms(
                [
                    new Transform(Constants::XMLDSIG_ENVELOPED),
                    new Transform(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS)
                ]
            ),
            new DigestMethod(Constants::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            'abc123',
            'someType',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'
        );

        $referenceElement = $reference->toXML();
        $this->assertEquals('abc123', $referenceElement->getAttribute('Id'));
        $this->assertEquals('someType', $referenceElement->getAttribute('Type'));
        $this->assertEquals('#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153', $referenceElement->getAttribute('URI'));

        $transformsElement = XMLUtils::xpQuery($referenceElement, './ds:Transforms');
        $this->assertCount(1, $transformsElement->childNodes);

        $transformElement = XMLUtils::xpQuery($transformsElement, './ds:Transform');
        $this->assertCount(2, $transformElement->childNodes);

        $this->assertEquals(Constants::XMLDSIG_ENVELOPED, $transformElement[0]->getAttribute('Algorithm'));
        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $transformElement[1]->getAttribute('Algorithm'));

        $digestMethodElement = XMLUtils::xpQuery($transformsElement, './ds:DigestMethod');
        $this->assertCount(1, $digestMethodElement->childNodes);
        $this->assertEquals(Constants::DIGEST_SHA256, $digestMethodElement[0]->getAttribute('Algorithm'));

        $digestValueElement = XMLUtils::xpQuery($transformsElement, './ds:DigestValue');
        $this->assertCount(1, $digestValueElement->childNodes);
        $this->assertEquals('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=', $digestValueElement[0]->textContent);


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
            new Transforms(
                [
                    new Transform(Constants::XMLDSIG_ENVELOPED),
                    new Transform(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS)
                ]
            ),
            new DigestMethod(Constants::DIGEST_SHA256),
            new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
            'abc123',
            'someType',
            '#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153'
        );

        $referenceElement = $reference->toXML();

        $this->assertEquals('ds:Transforms', $referenceElement[0]->tagName);
        $this->assertEquals('ds:DigestMethod', $referenceElement[1]->tagName);
        $this->assertEquals('ds:DigestValue', $referenceElement[2]->tagName);
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $reference = Reference::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('abc123', $reference->getId());
        $this->assertEquals('someType', $reference->getType());
        $this->assertEquals('#_1e280ee704fb1d8d9dec4bd6c1889ec96942921153', $reference->getURI());

        $transforms = $reference->getTransforms();
        $this->assertCount(1, $transforms);

        $transform = $transforms->getTransform();
        $this->assertCount(2, $transform);

        $this->assertEquals(Constants::XMLDSIG_ENVELOPED, $transform[0]->getAlgorithm());
        $this->assertEquals(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $transform[1]->getAlgorithm());

        $digestMethod = $transforms->getDigestMethod();
        $this->assertEquals(Constants::DIGEST_SHA256, $digestMethod[0]->getAlgorithm());

        $digestValue = $transforms->getDigestValue();
        $this->assertEquals('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=', $digestValue[0]->getDigest());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($reference)
        );
    }
}
