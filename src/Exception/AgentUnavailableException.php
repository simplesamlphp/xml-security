<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the agent is unreachable or temporarily unavailable
 * (HTTP 429, HTTP 5xx, network/TLS/connect errors).
 *
 * @package simplesamlphp/xml-security
 */
class AgentUnavailableException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
