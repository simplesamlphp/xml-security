<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

/**
 * Contract for supplying bearer tokens to the Private Key Agent.
 *
 * @package simplesamlphp/xml-security
 */
interface TokenProvider
{
    /**
     * Return the bearer token for the given key name.
     *
     * @param string $keyName The key name as resolved by a KeyNameResolver.
     *
     * @return string The bearer token.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\MissingTokenException If no token is available.
     */
    public function getToken(string $keyName): string;
}
