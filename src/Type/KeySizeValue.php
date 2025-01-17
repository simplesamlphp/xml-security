<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Type;

use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Type\IntegerValue;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * @package simplesaml/xml-security
 */
class KeySizeValue extends IntegerValue
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
        Assert::validKeySize($this->sanitizeValue($value), SchemaViolationException::class);
    }
}
