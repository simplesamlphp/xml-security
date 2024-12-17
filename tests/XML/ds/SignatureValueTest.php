<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\ds;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;
use SimpleSAML\XMLSecurity\XML\ds\SignatureValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\ds\SignatureValueTest
 *
 * @package simplesamlphp/xml-security
 */
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
        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval(new SignatureValue(
                'j14G9v6AnsOiEJYgkTg864DG3e/KLqoGpuybPGSGblVTn7ST6M/BsvP7YiVZjLqJEuEvWmf2mW4DPb+pbArzzDcsLWEtNveMrw+F' .
                'kWehDUQV9oe20iepo+W46wmj7zB/eWL+Z8MrGvlycoTndJU6CVwHTLsB+dq2FDa7JV4pAPjMY32JZTbiwKhzqw3nEi/eVrujJE4Y' .
                'RrlW28D+rXhITfoUAGGvsqPzcwGzp02lnMe2SmXADY1u9lbVjOhUrJpgvWfn9YuiCR+wjvaGMwIwzfJxChLJZOBV+1ad1CyNTiu6' .
                'qAblxZ4F8cWlMWJ7f0KkWvtw66HOf2VNR6Qan2Ra7Q==',
                'abc123',
            )),
        );
    }


    /**
     */
    public function testMarshallingNotBase64(): void
    {
        $this->expectException(AssertionFailedException::class);
        $digestValue = new SignatureValue(
            'j14G9v6AnsOiEJYgkTg864DG3e/KLqoGpuybPGSGblVTn7ST6M/BsvP7YiVZjLqJEuEvWmf2mW4DPb+pbArzzDcsLWEtNveMrw+F' .
            'kWehDUQV9oe20iepo+W46wmj7zB/eWL+Z8MrGvlycoTndJU6CVwHTLsB+dq2FDa7JV4pAPjMY32JZTbiwKhzqw3nEi/eVrujJE4Y' .
            'RrlW28D+rXhITfoUAGGvsqPzcwGzp02lnMe2SmXADY1u9lbVjOhUrJpgvWfn9YuiCR+wjvaGMwIwzfJxChLJZOBV+1ad1CyNTiu6' .
            'qblxZ4F8cWlMWJ7f0KkWvtw66HOf2VNR6Qan2Ra7Q==',
            'abc123',
        );
    }
}
