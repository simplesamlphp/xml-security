<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Type;

use SimpleSAML\XML\Type\IntegerValue;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * @package simplesaml/xml-security
 */
class HMACOutputLengthValue extends IntegerValue
{
    /**
     * Validate the value.
     *
     * @param string $value
     * @throws \SimpleSAML\XML\Exception\SchemaViolationException on failure
     * @throws \SimpleSAML\XMLSecurity\Exception\ProtocolViolationException when not devisible by 8
     * @return void
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        Assert::validHMACOutputLength($this->sanitizeValue($value));
    }
}
