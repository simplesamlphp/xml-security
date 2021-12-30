<?php

namespace SimpleSAML\XMLSecurity\Exception;

use Throwable;

/**
 * Class IOException
 *
 * This exception is thrown when an I/O operation cannot be handled
 *
 * @package simplesamlphp/xml-security
 */
class IOException extends InvalidArgumentException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'Generic I/O Exception.');
    }
}
