<?php

namespace SimpleSAML\SAML2\XML;

use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * An interface describing signable elements.
 *
 * @package simplesamlphp/xml-security
 */
interface SignableElementInterface
{
    /**
     * Retrieve the certificates that are included in the message.
     *
     * @return string[] An array of certificates
     */
    public function getCertificates(): array;


    /**
     * Set the certificates that should be included in the element.
     * The certificates should be strings with the PEM encoded data.
     *
     * @param string[] $certificates An array of certificates.
     */
    public function setCertificates(array $certificates): void;


    /**
     * Get the private key we should use to sign the message.
     *
     * If the key is null, the message will be sent unsigned.
     *
     * @return \SimpleSAML\XMLSecurity\XMLSecurityKey|null
     */
    public function getSigningKey(): ?XMLSecurityKey;


    /**
     * Set the private key we should use to sign the message.
     *
     * If the key is null, the message will be sent unsigned.
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey|null $signingKey
     */
    public function setSigningKey(XMLSecurityKey $signingKey = null): void;
}
