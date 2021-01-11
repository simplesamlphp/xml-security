<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use DOMNode;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Utils\Security as XMLSecurityUtils;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XMLSecurityKey;
use SimpleSAML\XML\Utils as XMLUtils;

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
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $signKey The private key used for signing.
     * @param array $certificates  Any public key to be added to the ds:Signature
     * @param \DOMNode|null $insertBefore  A specific node in the DOM structure where the ds:Signature should be put in front.
     * @return \DOMElement The signed element.
     * @throws \Exception If an error occurs while trying to sign.
     */
    private function toSignedXML(XMLSecurityKey $signKey, array $certificates, DOMNode $insertBefore = null): DOMElement
    {
        $root = $this->toXML();

        if ($insertBefore !== null) {
            XMLSecurityUtils::insertSignature($this->signingKey, $this->certificates, $root, $insertBefore);
            $doc = clone $root->ownerDocument;
        } else {
            $signature = new Signature($this->signingKey->getAlgorithm(), $this->certificates, $this->signingKey);
            $signature->toXML($root);
        }

        return $root;
    }
}
