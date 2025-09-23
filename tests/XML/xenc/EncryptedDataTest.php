<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Utils\XPath;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractEncryptedType;
use SimpleSAML\XMLSecurity\XML\xenc\AbstractXencElement;
use SimpleSAML\XMLSecurity\XML\xenc\CipherData;
use SimpleSAML\XMLSecurity\XML\xenc\CipherValue;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\xenc\EncryptedDataTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('xenc')]
#[CoversClass(AbstractXencElement::class)]
#[CoversClass(AbstractEncryptedType::class)]
#[CoversClass(EncryptedData::class)]
final class EncryptedDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = EncryptedData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/xenc_EncryptedData.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $encryptedData = new EncryptedData(
            new CipherData(
                new CipherValue(
                    Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                ),
            ),
            IDValue::fromString('MyID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            new EncryptionMethod(
                AnyURIValue::fromString(C::BLOCK_ENC_AES128),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            new CipherValue(
                                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                            ),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($encryptedData),
        );
    }


    /**
     */
    public function testMarshallingElementOrdering(): void
    {
        $encryptedData = new EncryptedData(
            new CipherData(
                new CipherValue(
                    Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                ),
            ),
            IDValue::fromString('MyID'),
            AnyURIValue::fromString(C::XMLENC_ELEMENT),
            StringValue::fromString('text/plain'),
            AnyURIValue::fromString('urn:x-simplesamlphp:encoding'),
            new EncryptionMethod(
                AnyURIValue::fromString(C::BLOCK_ENC_AES128),
            ),
            new KeyInfo(
                [
                    new EncryptedKey(
                        new CipherData(
                            new CipherValue(
                                Base64BinaryValue::fromString('/CTj03d1DB5e2t7CTo9BEzCf5S9NRzwnBgZRlm32REI='),
                            ),
                        ),
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        new EncryptionMethod(
                            AnyURIValue::fromString(C::SIG_RSA_SHA256),
                        ),
                    ),
                ],
            ),
        );

        $encryptedDataElement = $encryptedData->toXML();
        $xpCache = XPath::getXPath($encryptedDataElement);

        // Test for an EncryptionMethod
        $encryptedDataElements = XPath::xpQuery($encryptedDataElement, './xenc:EncryptionMethod', $xpCache);
        $this->assertCount(1, $encryptedDataElements);

        // Test ordering of EncryptedData contents
        /** @var \DOMElement[] $encryptedDataElements */
        $encryptedDataElements = XPath::xpQuery(
            $encryptedDataElement,
            './xenc:EncryptionMethod/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(2, $encryptedDataElements);
        $this->assertEquals('ds:KeyInfo', $encryptedDataElements[0]->tagName);
        $this->assertEquals('xenc:CipherData', $encryptedDataElements[1]->tagName);

        // EncryptionProperties is currently not supported
        //$this->assertEquals('xenc:EncryptionProperties', $encryptedDataElements[2]->tagName);
    }
}
