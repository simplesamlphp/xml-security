<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class UnsupportedAlgorithmException
 *
 * This exception is thrown when we can't handle a given algorithm
 *
 * @package simplesamlphp/xml-security
 */
class UnsupportedAlgorithmException extends RuntimeException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'Unsupported algorithm.');
    }
}
