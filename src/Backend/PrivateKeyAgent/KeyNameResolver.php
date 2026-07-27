<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent;

use SimpleSAML\XMLSecurity\Key\X509Certificate;

/**
 * Contract for resolving a PKA key name from an X.509 certificate.
 *
 * @package simplesamlphp/xml-security
 */
interface KeyNameResolver
{
    /** Pattern that valid key names must match. */
    public const string KEY_NAME_PATTERN = '/^[a-zA-Z0-9_-]{1,64}$/';


    /**
     * Resolve the agent key name for the given certificate.
     *
     * @param \SimpleSAML\XMLSecurity\Key\X509Certificate $certificate The certificate to resolve.
     *
     * @return string The key name (matches [a-zA-Z0-9_-]{1,64}).
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnknownKeyException If no key name can be resolved.
     */
    public function resolve(X509Certificate $certificate): string;
}
