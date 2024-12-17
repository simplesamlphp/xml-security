<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;
use SimpleSAML\XMLSecurity\XML\ds\SignedInfo;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignedInfoTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(SignedInfo::class)]
final class SignedInfoTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SignedInfo::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignedInfo.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $signedInfo = new SignedInfo(
            new CanonicalizationMethod(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
            new SignatureMethod(C::SIG_RSA_SHA256),
            [
                Reference::fromXML(
                    DOMDocumentFactory::fromFile(
                        dirname(__FILE__, 3) . '/resources/xml/ds_Reference.xml',
                    )->documentElement,
                ),
            ],
            'cba321',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signedInfo),
        );
    }


    /**
     *
     */
    private function canonicalization(DOMElement $xml, SignedInfo $signedInfo): void
    {
        $this->assertEquals(
            $xml->C14N(true, false),
            $signedInfo->canonicalize(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
        );
        $this->assertEquals(
            $xml->C14N(false, false),
            $signedInfo->canonicalize(C::C14N_INCLUSIVE_WITHOUT_COMMENTS),
        );
        $this->assertEquals(
            $xml->C14N(true, true),
            $signedInfo->canonicalize(C::C14N_EXCLUSIVE_WITH_COMMENTS),
        );
        $this->assertEquals(
            $xml->C14N(false, true),
            $signedInfo->canonicalize(C::C14N_INCLUSIVE_WITH_COMMENTS),
        );
    }


    /**
     * Test that canonicalization works fine.
     */
    public function testCanonicalization(): void
    {
        $xml =  DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignedInfoWithComments.xml',
        )->documentElement;
        $signedInfo = SignedInfo::fromXML($xml);
        $this->canonicalization($xml, $signedInfo);
    }


    /**
     * Test that canonicalization works fine even after serializing and unserializing
     */
    public function testCanonicalizationAfterSerialization(): void
    {
        $xml =  DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignedInfoWithComments.xml',
        )->documentElement;
        $signedInfo = unserialize(serialize(SignedInfo::fromXML($xml)));
        $this->canonicalization($xml, $signedInfo);
    }
}
