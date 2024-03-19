<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\SignatureProperties;
use SimpleSAML\XMLSecurity\XML\ds\SignatureProperty;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignaturePropertiesTest
 *
 * @package simplesamlphp/saml2
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(SignatureProperties::class)]
final class SignaturePropertiesTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SignatureProperties::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xmldsig1-schema.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignatureProperties.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<ssp:HSMSerialNumber xmlns:ssp="urn:x-simplesamlphp:namespace">1234567890</ssp:HSMSerialNumber>'
        );

        $signatureProperty = new SignatureProperty(
            [new Chunk($document->documentElement)],
            'https://simplesamlphp.org/some/target',
            'abc123'
        );
        $signatureProperties = new SignatureProperties([$signatureProperty], 'def456');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signatureProperties)
        );
    }
}
