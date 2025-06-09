<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XML\Type\{AnyURIValue, IDValue, IntegerValue, PositiveIntegerValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Type\{CryptoBinaryValue, ECPointValue};
use SimpleSAML\XMLSecurity\XML\dsig11\{AbstractDsig11Element, AbstractECKeyValueType};
use SimpleSAML\XMLSecurity\XML\dsig11\{A, B, Base, CoFactor, Curve, ECKeyValue, ECParameters, FieldID};
use SimpleSAML\XMLSecurity\XML\dsig11\{K1, K2, K3, M, Order, PnB, PublicKey, Seed, ValidationData};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\ECKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('dsig11')]
#[CoversClass(AbstractDsig11Element::class)]
#[CoversClass(AbstractECKeyValueType::class)]
#[CoversClass(ECKeyValue::class)]
final class ECKeyValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = ECKeyValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/dsig11_ECKeyValue.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        // Build FieldID
        $m = new M(PositiveIntegerValue::fromInteger(1024));
        $k1 = new K1(PositiveIntegerValue::fromInteger(128));
        $k2 = new K2(PositiveIntegerValue::fromInteger(256));
        $k3 = new K3(PositiveIntegerValue::fromInteger(512));
        $pnb = new PnB($m, $k1, $k2, $k3);

        $fieldId = new FieldID($pnb);

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

        // Build PublicKey
        $publicKey = new PublicKey(
            ECPointValue::fromString('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE='),
        );

        // Build ECKeyValue
        $ecKeyValue = new ECKeyValue(
            $publicKey,
            IDValue::fromString('phpunit'),
            $ecParameters,
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($ecKeyValue),
        );
    }
}
