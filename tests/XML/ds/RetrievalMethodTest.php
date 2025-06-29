<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\{AnyURIValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, RetrievalMethod};
use SimpleSAML\XMLSecurity\XML\ds\{Transform, Transforms, XPath};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\RetrievalMethodTest
 *
 * @package simplesamlphp/saml2
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(RetrievalMethod::class)]
final class RetrievalMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = RetrievalMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_RetrievalMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transforms = new Transforms([
            new Transform(
                AnyURIValue::fromString(C::XPATH10_URI),
                new XPath(
                    StringValue::fromString('self::xenc:CipherValue[@Id="example1"]'),
                ),
            ),
        ]);

        $retrievalMethod = new RetrievalMethod(
            $transforms,
            AnyURIValue::fromString('#Encrypted_KEY_ID'),
            AnyURIValue::fromString(C::XMLENC_ENCRYPTEDKEY),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($retrievalMethod),
        );
    }
}
