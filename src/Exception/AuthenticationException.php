<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the agent rejects the bearer token (HTTP 401).
 *
 * @package simplesamlphp/xml-security
 */
class AuthenticationException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
