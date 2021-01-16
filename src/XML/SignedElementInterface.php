<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

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
     * @return \SimpleSAML\XMLSecurity\XML\SignedElementInterface The signed element if we can verify the signature.
     */
    public function validate(XMLSecurityKey $key): SignedElementInterface;
}
