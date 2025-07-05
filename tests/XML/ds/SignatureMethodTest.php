<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Type\HMACOutputLengthValue;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, HMACOutputLength, SignatureMethod};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureMethodTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(SignatureMethod::class)]
final class SignatureMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SignatureMethod::class;

        self::$schemaFile = dirname(__FILE__, 3) . '/resources/schemas/simplesamlphp.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignatureMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $hmacOutputLength = new HMACOutputLength(
            HMACOutputLengthValue::fromString('128'),
        );

        $chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        )->documentElement);

        $signatureMethod = new SignatureMethod(
            AnyURIValue::fromString(C::SIG_RSA_SHA256),
            $hmacOutputLength,
            [$chunk],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signatureMethod),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $hmacOutputLength = new HMACOutputLength(
            HMACOutputLengthValue::fromString('128'),
        );

        $chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        )->documentElement);

        $signatureMethod = new SignatureMethod(
            AnyURIValue::fromString(C::SIG_RSA_SHA256),
            $hmacOutputLength,
            [$chunk],
        );

        $signatureMethodElement = $signatureMethod->toXML();

        $xpCache = XPath::getXPath($signatureMethodElement);

        $hmacOutputLength = XPath::xpQuery($signatureMethodElement, './ds:HMACOutputLength', $xpCache);
        $this->assertCount(1, $hmacOutputLength);

        /** @var \DOMElement[] $signatureMethodElements */
        $signatureMethodElements = XPath::xpQuery(
            $signatureMethodElement,
            './ds:HMACOutputLength/following-sibling::*',
            $xpCache,
        );

        // Test ordering of SignatureMethod contents
        $this->assertCount(1, $signatureMethodElements);
        $this->assertEquals('ssp:Chunk', $signatureMethodElements[0]->tagName);
    }
}
