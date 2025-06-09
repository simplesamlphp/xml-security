<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Test\XML\XMLDumper;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\DEREncodedKeyValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\DEREncodedKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(DEREncodedKeyValue::class)]
final class DEREncodedKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DEREncodedKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_DEREncodedKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $derEncodedKeyValue = new DEREncodedKeyValue(
            'MGYwHwYIKoUDBwEBAQEwEwYHKoUDAgIkAAYIKoUDBwEBAgIDQwAEQLrf0MNTFKvS'
            . 'j6pHRwtsQBdyu07oB36PZ+duQ9rOZhWXQ+acH/dP4uLxdJhZq/Z30cDGD+KND4NZ'
            . 'jp+UZWlzWK0=',
            'phpunit',
        );

        $this->assertEquals(
            XMLDumper::dumpDOMDocumentXMLWithBase64Content(self::$xmlRepresentation),
            strval($derEncodedKeyValue),
        );
    }
}
