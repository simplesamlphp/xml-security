<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use DOMDocument;
use SimpleSAML\Test\XML\SerializableXMLTest;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Class \SimpleSAML\XMLSecurity\XML\ds\KeyNameTest
 *
 * @covers \SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement
 * @covers \SimpleSAML\XMLSecurity\XML\ds\KeyName
 *
 * @package simplesamlphp/xml-security
 */
final class KeyNameTest extends SerializableXMLTest
{
    /**
     */
    protected function setUp(): void
    {
        self::$element = KeyName::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(dirname(dirname(__FILE__)))) . '/tests/resources/xml/ds_KeyName.xml'
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $keyName = new KeyName('testkey');

        $this->assertEquals('testkey', $keyName->getName());

        $this->assertEquals(self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement), strval($keyName));
    }


    /**
     */
    public function testUnmarshalling(): void
    {
        $keyName = KeyName::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals('testkey', $keyName->getName());
    }
}
