<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue};
use SimpleSAML\XMLSecurity\XML\ds\{AbstractDsElement, SignatureValue};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[Group('ds')]
#[CoversClass(AbstractDsElement::class)]
#[CoversClass(SignatureValue::class)]
final class SignatureValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     * Set up the test.
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SignatureValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 3) . '/resources/xml/ds_SignatureValue.xml',
        );
    }


    /**
     * Test creating a SignatureValue from scratch.
     */
    public function testMarshalling(): void
    {
        $signatureValue = new SignatureValue(
            Base64BinaryValue::fromString(
                'j14G9v6AnsOiEJYgkTg864DG3e/KLqoGpuybPGSGblVTn7ST6M/BsvP7YiVZjLqJEuEvWmf2mW4DPb+pbArzzDcsLWEtNveMrw+F' .
                'kWehDUQV9oe20iepo+W46wmj7zB/eWL+Z8MrGvlycoTndJU6CVwHTLsB+dq2FDa7JV4pAPjMY32JZTbiwKhzqw3nEi/eVrujJE4Y' .
                'RrlW28D+rXhITfoUAGGvsqPzcwGzp02lnMe2SmXADY1u9lbVjOhUrJpgvWfn9YuiCR+wjvaGMwIwzfJxChLJZOBV+1ad1CyNTiu6' .
                'qAblxZ4F8cWlMWJ7f0KkWvtw66HOf2VNR6Qan2Ra7Q==',
            ),
            IDValue::fromString('abc123'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($signatureValue),
        );
    }
}
