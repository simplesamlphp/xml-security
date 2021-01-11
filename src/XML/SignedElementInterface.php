<?php

namespace SimpleSAML\SAML2\XML;

use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * An interface describing signed elements.
 *
 * @package simplesamlphp/xml-security
 */
interface SignedElementInterface
{
    /**
     * Retrieve certificates that sign this element.
     *
     * @return array Array with certificates.
     * @throws \Exception if an error occurs while trying to extract the public key from a certificate.
     */
    public function getValidatingCertificates(): array;


    /**
     * Validate this element against a public key.
     *
     * If no signature is present, false is returned. If a signature is present,
     * but cannot be verified, an exception will be thrown.
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $key The key we should check against.
     * @return bool True if successful, false if we don't have a signature that can be verified.
     */
    public function validate(XMLSecurityKey $key): bool;
}
