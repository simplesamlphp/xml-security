<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Type;

use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Type\IntegerValue;
use SimpleSAML\XMLSecurity\Assert\Assert;
use SimpleSAML\XMLSecurity\Exception\ProtocolViolationException;

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
     * @return void
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        $value = $this->sanitizeValue($value);

        Assert::validHMACOutputLength($value, SchemaViolationException::class);
        Assert::true(
            intval($value) % 8 === 0,
            '%s is not devisible by 8 and therefore not a valid ds:HMACOutputLengthType',
            ProtocolViolationException::class,
        );
    }
}
