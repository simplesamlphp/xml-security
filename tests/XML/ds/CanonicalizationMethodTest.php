<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\Builtin\AnyURIValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, CanonicalizationMethod};

use function dirname;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\CanonicalizationMethodTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(CanonicalizationMethod::class)]
final class CanonicalizationMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = CanonicalizationMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_CanonicalizationMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $canonicalizationMethod = new CanonicalizationMethod(
            AnyURIValue::fromString(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($canonicalizationMethod),
        );
    }
}
