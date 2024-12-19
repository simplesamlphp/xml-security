<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\DigestValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(DigestValue::class)]
final class DigestValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DigestValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_DigestValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $digestValue = new DigestValue('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($digestValue),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(AssertionFailedException::class);
        new DigestValue('/CTj3d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI=');
    }
}
