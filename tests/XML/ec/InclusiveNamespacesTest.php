<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ec;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\NMTokensValue;
use SimpleSAML\XMLSecurity\XML\ec\AbstractEcElement;
use SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ec\InclusiveNamespacesTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ec')]
#[CoversClass(AbstractEcElement::class)]
#[CoversClass(InclusiveNamespaces::class)]
class InclusiveNamespacesTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = InclusiveNamespaces::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ec_InclusiveNamespaces.xml',
        );
    }


    public function testMarshalling(): void
    {
        $inclusiveNamespaces = new InclusiveNamespaces(
            NMTokensValue::fromString("dsig soap"),
        );

        $this->assertEquals("dsig soap", strval($inclusiveNamespaces->getPrefixes()));

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($inclusiveNamespaces),
        );
    }
}
