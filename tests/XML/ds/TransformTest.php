<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\NMTokensValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\XPath;
use SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces;
use SimpleSAML\XPath\Constants as XPATH_C;

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
        // With XPath
        $transform = new Transform(
            AnyURIValue::fromString(XPATH_C::XPATH10_URI),
            XPath::fromString('count(//. | //@* | //namespace::*)'),
        );

        $expectedXml = self::$xmlRepresentation->saveXml(self::$xmlRepresentation->documentElement);
        $this->assertNotFalse($expectedXml);
        $actualXml = strval($transform);

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);

        // Same test with InclusiveNamespaces
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

        $expectedXml = $xmlRepresentation->saveXml($xmlRepresentation->documentElement);
        $this->assertNotFalse($expectedXml);
        $actualXml = strval($transform);

        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
    }
}
