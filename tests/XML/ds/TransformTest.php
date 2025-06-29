<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{AnyURIValue, NMTokensValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, Transform, XPath};
use SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\TransformTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(Transform::class)]
final class TransformTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Transform::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Transform.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $transform = new Transform(
            AnyURIValue::fromString(C::XPATH10_URI),
            new XPath(
                StringValue::fromString('count(//. | //@* | //namespace::*)'),
            ),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($transform),
        );

        $transform = new Transform(
            AnyURIValue::fromString(C::C14N_EXCLUSIVE_WITHOUT_COMMENTS),
            null,
            new InclusiveNamespaces(
                NMTokensValue::fromString("dsig soap"),
            ),
        );


        $this->assertInstanceOf(InclusiveNamespaces::class, $transform->getInclusiveNamespaces());
        $this->assertNull($transform->getXPath());

        $xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_Transform_InclusiveNamespaces.xml',
        );

        $this->assertEquals(
            $xmlRepresentation->saveXML($xmlRepresentation->documentElement),
            strval($transform),
        );
    }
}
