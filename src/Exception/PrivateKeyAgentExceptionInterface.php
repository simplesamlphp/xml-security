<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Marker interface for all Private Key Agent interaction exceptions.
 *
 * Consumers can catch all agent-interaction failures in a single
 * catch (PrivateKeyAgentExceptionInterface $e) block without relying
 * on the broad RuntimeException hierarchy.
 *
 * @package simplesamlphp/xml-security
 */
interface PrivateKeyAgentExceptionInterface
{
}
