<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the authenticated client is not authorised to use the requested key (HTTP 403).
 *
 * @package simplesamlphp/xml-security
 */
class AuthorizationException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
