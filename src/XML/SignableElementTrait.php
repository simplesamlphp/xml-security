<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use DOMNode;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\Utils\Security as XMLSecurityUtils;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Helper trait for processing signed elements.
 *
 * @package simplesamlphp/xml-security
 */
trait SignableElementTrait
{
    /**
     * Sign the given XML element.
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $signingKey The private key used for signing.
     * @param array $certificates  Any public key to be added to the ds:Signature
     * @param \DOMNode|null $insertBefore  A specific node in the DOM structure where the ds:Signature should be put in front.
     * @return \DOMElement The signed element.
     * @throws \Exception If an error occurs while trying to sign.
     */
    private function toSignedXML(XMLSecurityKey $signingKey, array $certificates, DOMNode $insertBefore = null): DOMElement
    {
        $root = $this->toXML();

        if ($insertBefore !== null) {
            XMLSecurityUtils::insertSignature($signingKey, $certificates, $root, $insertBefore);
        } else {
            $signature = new Signature($signingKey->getAlgorithm(), $certificates, $signingKey);
            $signature->toXML($root);
        }

        return $root;
    }
}
