<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Assert;

use InvalidArgumentException;

/**
 * @package simplesamlphp/xml-security
 */
trait HMACOutputLengthTrait
{
    /**
     * The HMAC algorithm (RFC2104 [HMAC]) takes the output (truncation) length in bits as a parameter;
     * this specification REQUIRES that the truncation length be a multiple of 8 (i.e. fall on a byte boundary)
     * because Base64 encoding operates on full bytes
     *
     * @var string
     */
    private static string $HMACOutputLength_regex = '/^([1-9]\d*)$/D';


    /**
     * @param string $value
     * @param string $message
     */
    protected static function validHMACOutputLength(string $value, string $message = ''): void
    {
        parent::regex(
            $value,
            self::$HMACOutputLength_regex,
            $message ?: '%s is not a valid ds:HMACOutputLengthType',
            InvalidArgumentException::class,
        );
    }
}
