<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;
use SimpleSAML\XMLSecurity\XML\ds\SignedInfo;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignedInfoTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\SignedInfo
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 *
 * @package simplesamlphp/xml-security
 */
final class SignedInfoTest extends TestCase
{
    use SerializableXMLTestTrait;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = SignedInfo::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_SignedInfo.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $signedInfo = new SignedInfo(
            new CanonicalizationMethod(Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
            new SignatureMethod(Constants::SIG_RSA_SHA256),
            [
                Reference::fromXML(
                    DOMDocumentFactory::fromFile(
                        dirname(dirname(dirname(__FILE__))) . '/resources/xml/ds_Reference.xml'
                    )->documentElement
                )
            ],
            'abc123'
        );

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signedInfo)
        );
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $signedInfo = SignedInfo::fromXML($this->xmlRepresentation->documentElement);
        $this->assertEquals('abc123', $signedInfo->getId());

        $this->assertEquals(
            Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
            $signedInfo->getCanonicalizationMethod()->getAlgorithm()
        );
        $this->assertEquals(
            Constants::SIG_RSA_SHA256,
            $signedInfo->getSignatureMethod()->getAlgorithm()
        );

        $references = $signedInfo->getReferences();
        $this->assertCount(1, $references);

        $this->assertEquals(
            $this->xmlRepresentation->saveXML($this->xmlRepresentation->documentElement),
            strval($signedInfo)
        );
    }
}
