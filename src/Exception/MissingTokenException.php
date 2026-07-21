<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the TokenProvider cannot supply a bearer token for the requested key.
 *
 * Distinct from AuthenticationException (which means the agent rejected the token);
 * this means the client side could not produce a token at all.
 *
 * @package simplesamlphp/xml-security
 */
class MissingTokenException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
