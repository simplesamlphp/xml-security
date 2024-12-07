<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc11;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Utils\XPath as XPathUtils;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;
use SimpleSAML\XMLSecurity\XML\ds\XPath;
use SimpleSAML\XMLSecurity\XML\xenc\DataReference;
use SimpleSAML\XMLSecurity\XML\xenc\KeyReference;
use SimpleSAML\XMLSecurity\XML\xenc\ReferenceList;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractDerivedKeyType;
use SimpleSAML\XMLSecurity\XML\xenc11\AbstractXenc11Element;
use SimpleSAML\XMLSecurity\XML\xenc11\DerivedKey;
use SimpleSAML\XMLSecurity\XML\xenc11\DerivedKeyName;
use SimpleSAML\XMLSecurity\XML\xenc11\KeyDerivationMethod;
use SimpleSAML\XMLSecurity\XML\xenc11\MasterKeyName;

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
#[CoversClass(AbstractXenc11Element::class)]
#[CoversClass(AbstractDerivedKeyType::class)]
#[CoversClass(DerivedKey::class)]
final class DerivedKeyTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DerivedKey::class;

        self::$schemaFile = dirname(__FILE__, 4) . '/resources/schemas/xenc-schema-11.xsd';

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc11_DerivedKey.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $alg = 'http://www.w3.org/2009/xmlenc11#ConcatKDF';
        $keyName = new KeyName('testkey');

        $keyDerivationMethod = new KeyDerivationMethod($alg, [$keyName]);

        $transformData = new Transform(
            C::XPATH10_URI,
            new XPath('self::xenc:EncryptedData[@Id="example1"]'),
        );
        $transformKey = new Transform(
            C::XPATH10_URI,
            new XPath('self::xenc:EncryptedKey[@Id="example1"]'),
        );

        $referenceList = new ReferenceList(
            [
                new DataReference('#Encrypted_DATA_ID', [new Transforms([$transformData])]),
            ],
            [
                new KeyReference('#Encrypted_KEY_ID', [new Transforms([$transformKey])]),
            ],
        );

        $derivedKeyName = new DerivedKeyName('phpunit');
        $masterKeyName = new MasterKeyName('phpunit');

        $derivedKey = new DerivedKey(
            'phpunit',
            'phpunit',
            'urn:x-simplesamlphp:type',
            $keyDerivationMethod,
            $referenceList,
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
        $alg = 'http://www.w3.org/2009/xmlenc11#ConcatKDF';
        $keyName = new KeyName('testkey');

        $keyDerivationMethod = new KeyDerivationMethod($alg, [$keyName]);

        $transformData = new Transform(
            C::XPATH10_URI,
            new XPath('self::xenc:EncryptedData[@Id="example1"]'),
        );
        $transformKey = new Transform(
            C::XPATH10_URI,
            new XPath('self::xenc:EncryptedKey[@Id="example1"]'),
        );

        $referenceList = new ReferenceList(
            [
                new DataReference('#Encrypted_DATA_ID', [new Transforms([$transformData])]),
            ],
            [
                new KeyReference('#Encrypted_KEY_ID', [new Transforms([$transformKey])]),
            ],
        );

        $derivedKeyName = new DerivedKeyName('phpunit');
        $masterKeyName = new MasterKeyName('phpunit');

        $derivedKey = new DerivedKey(
            'phpunit',
            'phpunit',
            'urn:x-simplesamlphp:type',
            $keyDerivationMethod,
            $referenceList,
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
