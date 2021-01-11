<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

//use DOMElement;
//use DOMNode;
//use Exception;
use SimpleSAML\Assert\Assert;
//use SimpleSAML\XMLSecurity\Utils\Security as XMLSecurityUtils;
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
     * List of certificates that should be included in the message.
     *
     * @var string[]
     */
    protected array $certificates = [];

    /**
     * The private key we should use to sign an unsigned message.
     *
     * The private key can be null, in which case we can only validate an already signed message.
     *
     * @var \SimpleSAML\XMLSecurity\XMLSecurityKey|null
     */
    protected ?XMLSecurityKey $signingKey = null;


    /**
     * Retrieve the certificates that are included in the message.
     *
     * @return string[] An array of certificates
     */
    public function getCertificates(): array
    {
        return $this->certificates;
    }


    /**
     * Set the certificates that should be included in the element.
     * The certificates should be strings with the PEM encoded data.
     *
     * @param string[] $certificates An array of certificates.
     */
    public function setCertificates(array $certificates): void
    {
        Assert::allStringNotEmpty($certificates);

        $this->certificates = $certificates;
    }


    /**
     * Get the private key we should use to sign the message.
     *
     * If the key is null, the message will be sent unsigned.
     *
     * @return \SimpleSAML\XMLSecurity\XMLSecurityKey|null
     */
    public function getSigningKey(): ?XMLSecurityKey
    {
        return $this->signingKey;
    }


    /**
     * Set the private key we should use to sign the message.
     *
     * If the key is null, the message will be sent unsigned.
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey|null $signingKey
     */
    public function setSigningKey(XMLSecurityKey $signingKey = null): void
    {
        $this->signingKey = $signingKey;
    }


    /**
     * Sign the given XML element.
     *
     * @param \DOMElement $root The element we should sign.
     * @return \DOMElement The signed element.
     * @throws \Exception If an error occurs while trying to sign.
    protected function signElement(DOMElement $root, DOMNode $insertBefore = null): DOMElement
    {
        if ($this->signingKey instanceof XMLSecurityKey) {
            if ($insertBefore !== null) {
                XMLSecurityUtils::insertSignature($this->signingKey, $this->certificates, $root, $insertBefore);

                $doc = clone $root->ownerDocument;
                $this->signature = Signature::fromXML(XMLUtils::xpQuery($doc->documentElement, './ds:Signature')[0]);
            } else {
                $this->signature = new Signature($this->signingKey->getAlgorithm(), $this->certificates, $this->signingKey);
                $this->signature->toXML($root);
            }
        }
        return $root;
    }
     */
}
