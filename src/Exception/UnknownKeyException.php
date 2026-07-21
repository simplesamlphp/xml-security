<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the requested key name is not registered in the agent (HTTP 404),
 * or when a key-name resolver cannot map a certificate to a key name.
 *
 * @package simplesamlphp/xml-security
 */
class UnknownKeyException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
