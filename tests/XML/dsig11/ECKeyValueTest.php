<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\dsig11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\dsig11\A;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractDsig11Element;
use SimpleSAML\XMLSecurity\XML\dsig11\AbstractECKeyValueType;
use SimpleSAML\XMLSecurity\XML\dsig11\B;
use SimpleSAML\XMLSecurity\XML\dsig11\Base;
use SimpleSAML\XMLSecurity\XML\dsig11\CoFactor;
use SimpleSAML\XMLSecurity\XML\dsig11\Curve;
use SimpleSAML\XMLSecurity\XML\dsig11\ECKeyValue;
use SimpleSAML\XMLSecurity\XML\dsig11\ECParameters;
use SimpleSAML\XMLSecurity\XML\dsig11\FieldID;
use SimpleSAML\XMLSecurity\XML\dsig11\K1;
use SimpleSAML\XMLSecurity\XML\dsig11\K2;
use SimpleSAML\XMLSecurity\XML\dsig11\K3;
use SimpleSAML\XMLSecurity\XML\dsig11\M;
use SimpleSAML\XMLSecurity\XML\dsig11\Order;
use SimpleSAML\XMLSecurity\XML\dsig11\PnB;
use SimpleSAML\XMLSecurity\XML\dsig11\PublicKey;
use SimpleSAML\XMLSecurity\XML\dsig11\Seed;
use SimpleSAML\XMLSecurity\XML\dsig11\ValidationData;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\dsig11\ECKeyValueTest
 *
 * @package simplesamlphp/xml-security
 */
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
        $m = new M(1024);
        $k1 = new K1(128);
        $k2 = new K2(256);
        $k3 = new K3(512);
        $pnb = new PnB($m, $k1, $k2, $k3);

        $fieldId = new FieldID($pnb);

        // Build Curve
        $a = new A('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');
        $b = new B('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');
        $curve = new Curve($a, $b);

        // Build Base
        $base = new Base('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');

        // Build Order
        $order = new Order('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');

        // Build CoFactor
        $coFactor = new CoFactor(128);

        // Build ValidationData
        $seed = new Seed('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');
        $validationData = new ValidationData($seed, C::DIGEST_SHA1);

        // Build ECParameters
        $ecParameters = new ECParameters($fieldId, $curve, $base, $order, $coFactor, $validationData);

        // Build PublicKey
        $publicKey = new PublicKey('6tN39Q9d6IevlAWLeM7lQGazUnVlJOe1wCk3sro2rfE=');

        // Build ECKeyValue
        $ecKeyValue = new ECKeyValue($publicKey, 'phpunit', $ecParameters);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($ecKeyValue),
        );
    }
}
