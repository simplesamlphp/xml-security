<?php

namespace SimpleSAML\XMLSecurity\Exception;

use RuntimeException as BuiltinRuntimeException;
use Throwable;

/**
 * Class RuntimeException
 *
 * This exception is thrown when an error occurs during processing in the library.
 *
 * @package simplesamlphp/xml-security
 */
class RuntimeException extends BuiltinRuntimeException implements Throwable
{
}
