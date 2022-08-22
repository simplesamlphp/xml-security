<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMElement;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\ds\XPath;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\RetrievalMethodTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/saml2
 */
final class RetrievalMethodTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = RetrievalMethod::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_RetrievalMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transforms = new Transforms(
            [new Transform(C::XPATH_URI, new XPath('self::xenc:CipherValue[@Id="example1"]', ['xenc' => C::NS_XENC]))],
        );

        $retrievalMethod = new RetrievalMethod($transforms, '#Encrypted_KEY_ID', C:: XMLENC_ENCRYPTEDKEY);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($retrievalMethod),
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $retrievalMethod = retrievalMethod::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('#Encrypted_KEY_ID', $retrievalMethod->getURI());
        $this->assertEquals(C::XMLENC_ENCRYPTEDKEY, $retrievalMethod->getType());

        $transforms = $retrievalMethod->getTransforms();
        $this->assertNotNull($transforms);

        $transform = $transforms->getTransform();
        $this->assertCount(1, $transform);

        $transformElement = reset($transform);
        $this->assertEquals(C::XPATH_URI, $transformElement->getAlgorithm());

        $xpath = $transformElement->getXPath();
        $this->assertNotNull($xpath);

        $this->assertEquals('self::xenc:CipherValue[@Id="example1"]', $xpath->getExpression());

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($retrievalMethod),
        );
    }
}
