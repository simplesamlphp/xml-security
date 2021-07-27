<?php

namespace SimpleSAML\XMLSecurity\Exception;

use Throwable;

/**
 * Class FileNotFoundException
 *
 * This exception is thrown when a file cannot be efound on the file system
 *
 * @package simplesamlphp/xml-security
 */
class FileNotFoundException extends InvalidArgumentException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'File not found.');
    }
}
