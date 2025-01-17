<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{AnyURIValue, Base64BinaryValue, IDValue, StringValue};
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Utils\XPath as XPathUtils;
use SimpleSAML\XMLSecurity\XML\ds\{Exponent, Modulus, RSAKeyValue, Transform, Transforms, XPath};
use SimpleSAML\XMLSecurity\XML\xenc\{DataReference, KeyReference, ReferenceList};
use SimpleSAML\XMLSecurity\XML\xenc11\{
    AbstractDerivedKeyType,
    AbstractXenc11Element,
    DerivedKey,
    DerivedKeyName,
    MasterKeyName,
    KeyDerivationMethod,
};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc11\DerivedKeyTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\AbstractDerivedKeyType
 * @covers \SimpleSAML\XMLSecurity\XML\xenc11\DerivedKey
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc11')]
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(AbstractDerivedKeyType::class)]
#[CoversClass(DerivedKey::class)]
final class DerivedKeyTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    private static KeyDerivationMethod $keyDerivationMethod;
    private static ReferenceList $referenceList;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DerivedKey::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_DerivedKey.xml',
        );

        self::$keyDerivationMethod = new KeyDerivationMethod(
            AnyURIValue::fromString(C::KEY_DERIVATION_CONCATKDF),
            [
                new RSAKeyValue(
                    new Modulus(
                        Base64BinaryValue::fromString('dGhpcyBpcyBzb21lIHJhbmRvbSBtb2R1bHVzCg=='),
                    ),
                    new Exponent(
                        Base64BinaryValue::fromString('dGhpcyBpcyBzb21lIHJhbmRvbSBleHBvbmVudAo='),
                    ),
                ),
            ],
        );

        $transformData = new Transform(
            AnyURIValue::fromString(C::XPATH10_URI),
            new XPath(
                StringValue::fromString('self::xenc:EncryptedData[@Id="example1"]'),
            ),
        );
        $transformKey = new Transform(
            AnyURIValue::fromString(C::XPATH10_URI),
            new XPath(
                StringValue::fromString('self::xenc:EncryptedKey[@Id="example1"]'),
            ),
        );

        self::$referenceList = new ReferenceList(
            [
                new DataReference(
                    AnyURIValue::fromString('#Encrypted_DATA_ID'),
                    [new Transforms([$transformData])],
                ),
            ],
            [
                new KeyReference(
                    AnyURIValue::fromString('#Encrypted_KEY_ID'),
                    [new Transforms([$transformKey])],
                ),
            ],
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $derivedKeyName = new DerivedKeyName(
            StringValue::fromString('phpunit'),
        );
        $masterKeyName = new MasterKeyName(
            StringValue::fromString('phpunit'),
        );

        $derivedKey = new DerivedKey(
            StringValue::fromString('phpunit'),
            IDValue::fromString('phpunit'),
            AnyURIValue::fromString('urn:x-simplesamlphp:type'),
            self::$keyDerivationMethod,
            self::$referenceList,
            $derivedKeyName,
            $masterKeyName,
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($derivedKey),
        );
    }


    /**
     */
    public function testMarshallingElementOrder(): void
    {
        $derivedKeyName = new DerivedKeyName(
            StringValue::fromString('phpunit'),
        );
        $masterKeyName = new MasterKeyName(
            StringValue::fromString('phpunit'),
        );

        $derivedKey = new DerivedKey(
            StringValue::fromString('phpunit'),
            IDValue::fromString('phpunit'),
            AnyURIValue::fromString('urn:x-simplesamlphp:type'),
            self::$keyDerivationMethod,
            self::$referenceList,
            $derivedKeyName,
            $masterKeyName,
        );

        $dkElement = $derivedKey->toXML();
        $xpCache = XPathUtils::getXPath($dkElement);

        // Test for a KeyDerivationMethod
        /** @var \DOMElement[] $keyDerivationMethodElements */
        $keyDerivationMethodElements = XPathUtils::xpQuery($dkElement, './xenc11:KeyDerivationMethod', $xpCache);
        $this->assertCount(1, $keyDerivationMethodElements);

        // Test ordering of DerivedKey contents
        /** @var \DOMElement[] $dkElements */
        $dkElements = XPathUtils::xpQuery($dkElement, './xenc11:KeyDerivationMethod/following-sibling::*', $xpCache);

        $this->assertCount(3, $dkElements);
        $this->assertEquals('xenc:ReferenceList', $dkElements[0]->tagName);
        $this->assertEquals('xenc11:DerivedKeyName', $dkElements[1]->tagName);
        $this->assertEquals('xenc11:MasterKeyName', $dkElements[2]->tagName);
    }


    /**
     * Adding an empty DerivedKey element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $xenc11_ns = DerivedKey::NS;
        $derivedKey = new DerivedKey();
        $this->assertEquals(
            "<xenc11:DerivedKey xmlns:xenc11=\"$xenc11_ns\"/>",
            strval($derivedKey),
        );
        $this->assertTrue($derivedKey->isEmptyElement());
    }
}
