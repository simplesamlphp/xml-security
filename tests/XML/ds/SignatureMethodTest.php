<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\HMACOutputLength;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureMethodTest
 *
 * @package simplesamlphp/xml-security
 */
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
        $hmacOutputLength = new HMACOutputLength('1234');

        $chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">Some</ssp:Chunk>',
        )->documentElement);

        $signatureMethod = new SignatureMethod(C::SIG_RSA_SHA256, $hmacOutputLength, [$chunk]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signatureMethod),
        );
    }
}
