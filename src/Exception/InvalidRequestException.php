<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when the agent rejects the request as invalid (HTTP 400).
 *
 * This includes invalid JSON, unknown algorithm, wrong hash length,
 * invalid base64 encoding, or a failed decryption operation.
 *
 * @package simplesamlphp/xml-security
 */
class InvalidRequestException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
