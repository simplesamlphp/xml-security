<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Type;

use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * @package simplesaml/xml-security
 */
class DigestValueValue extends Base64BinaryValue
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
        Assert::validDigestValue($this->sanitizeValue($value), SchemaViolationException::class);
    }
}
