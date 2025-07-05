<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, AbstractECValidationDataType};
use SimpleSAML\XMLSecurity\XML\dsig11\{Seed, ValidationData};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\ValidationDataTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractECValidationDataType::class)]
#[CoversClass(ValidationData::class)]
final class ValidationDataTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = ValidationData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_ValidationData.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $seed = new Seed(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $validationData = new ValidationData(
            $seed,
            AnyURIValue::fromString(C::DIGEST_SHA1),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($validationData),
        );
    }
}
