<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Type;

use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Assert\Assert;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;

/**
 * @package simplesaml/xml-security
 */
class ECPointValue extends CryptoBinaryValue
{
    /**
     * Validate the value.
     *
     * @param string $value
     * @throws \SimpleSAML\XML\Exception\SchemaViolationException on failure
     * @return void
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        Assert::validECPoint($this->sanitizeValue($value), SchemaViolationException::class);
    }
}
