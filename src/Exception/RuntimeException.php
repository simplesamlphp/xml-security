<?php

namespace SimpleSAML\XMLSecurity\Exception;

use RuntimeException as BUILTIN_RuntimeException;
use Throwable;

/**
 * Class RuntimeException
 *
 * This exception is thrown when an error occurs during processing in the library.
 *
 * @package SimpleSAML\XMLSecurity\Exception
 */
class RuntimeException extends BUILTIN_RuntimeException implements Throwable
{
}
