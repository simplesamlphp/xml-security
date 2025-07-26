<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\{AnyURIValue, IntegerValue, PositiveIntegerValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Type\{CryptoBinaryValue, ECPointValue};
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, AbstractECParametersType};
use SimpleSAML\XMLSecurity\XML\dsig11\{A, B, Base, CoFactor, Curve, ECParameters, FieldID};
use SimpleSAML\XMLSecurity\XML\dsig11\{K, M, Order, Seed, TnB, ValidationData};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\ECParametersTest
 *
 * @package simplesamlphp/xml-security
 */

#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractECParametersType::class)]
#[CoversClass(ECParameters::class)]
final class ECParametersTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = ECParameters::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_ECParameters.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        // Build FieldID
        $m = new M(PositiveIntegerValue::fromInteger(1024));
        $k = new K(PositiveIntegerValue::fromInteger(64));
        $tnb = new TnB($m, $k);

        $fieldId = new FieldID($tnb);

        // Build Curve
        $a = new A(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $b = new B(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $curve = new Curve($a, $b);

        // Build Base
        $base = new Base(
            ECPointValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );

        // Build Order
        $order = new Order(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );

        // Build CoFactor
        $coFactor = new CoFactor(IntegerValue::fromInteger(128));

        // Build ValidationData
        $seed = new Seed(
            CryptoBinaryValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );
        $validationData = new ValidationData(
            $seed,
            AnyURIValue::fromString(C::DIGEST_SHA1),
        );

        // Build ECParameters
        $ecParameters = new ECParameters($fieldId, $curve, $base, $order, $coFactor, $validationData);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($ecParameters),
        );
    }
}
