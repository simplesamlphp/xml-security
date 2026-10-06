<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the agent returns an unexpected or malformed response
 * (e.g. invalid JSON, missing fields, invalid base64, or an unexpected HTTP status code).
 *
 * @package simplesamlphp/xml-security
 */
class AgentProtocolException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
