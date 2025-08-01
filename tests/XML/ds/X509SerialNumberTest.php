<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\IntegerValue;
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, X509SerialNumber};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\X509SerialNumberTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(X509SerialNumber::class)]
final class X509SerialNumberTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = X509SerialNumber::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_X509SerialNumber.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $serialNumber = new X509SerialNumber(
            IntegerValue::fromString('123456'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($serialNumber),
        );
    }


    /**
     */
    public function testUnmarshallingIncorrectTypeThrowsException(): void
    {
        $document = clone self::$xmlRepresentation;
        /** @var \DOMElement $docElement */
        $docElement = $document->documentElement;
        $docElement->textContent = 'Not an integer';

        $this->expectException(SchemaViolationException::class);
        X509SerialNumber::fromXML($docElement);
    }
}
