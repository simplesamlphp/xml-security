<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Thrown when a signature returned by the agent does not verify against the public key of the
 * certificate the signing operation was given.
 *
 * Indicates that the agent signed with a different key than the caller intended (a mismatched key
 * name or a substituted key), or that the response was tampered with in transit.
 *
 * @package simplesamlphp/xml-security
 */
class AgentSignatureMismatchException extends RuntimeException implements PrivateKeyAgentExceptionInterface
{
}
