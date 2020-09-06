<?php

namespace SimpleSAML\XMLSecurity\Exception;

use Throwable;

/**
 * Class InvalidArgumentException
 *
 * This exception is thrown when a parameter is passed to a method with the wrong type or contents.
 *
 * @package SimpleSAML\XMLSecurity\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements Throwable
{
    /**
     * @param string $expected description of expected type
     * @param mixed  $parameter the parameter that is not of the expected type.
     *
     * @return \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException
     */
    public static function invalidType(string $expected, $parameter): InvalidArgumentException
    {
        $message = sprintf(
            'Invalid Argument type: "%s" expected, "%s" given',
            $expected,
            is_object($parameter) ? get_class($parameter) : gettype($parameter)
        );

        return new self($message);
    }
}
