<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * An interface describing signable elements.
 *
 * @package simplesamlphp/xml-security
 */
interface SignableElementInterface
{
    /**
     * Sign the 'Element' and return a 'SignedElement'
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $signingKey  The private key we should use to sign the message
     * @param string[] $certificates  The certificates should be strings with the PEM encoded data
     * @return \SimpleSAML\XMLSecurity\XML\SignedElementInterface
     */
    public function sign(XMLSecurityKey $signingKey, array $certificates): SignedElementInterface;
}
