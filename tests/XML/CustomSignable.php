<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\XML\SignableElementInterface;
use SimpleSAML\XMLSecurity\XML\SignedElementInterface;
use SimpleSAML\XMLSecurity\XMLSecurityKey;
use SimpleSAML\XMLSecurity\XML\ds\Signature;

/**
 * @package simplesamlphp\saml2
 */
final class CustomSignable extends AbstractXMLElement implements SignableElementInterface
{
    /** @var string */
    public const NS = 'urn:ssp:custom';

    /** @var string */
    public const NS_PREFIX = 'ssp';

    /** @var \DOMElement $element */
    protected $element;

    /**
     * Constructor
     *
     * @param \DOMElement $elt
     */
    public function __construct(DOMElement $elt) {
        $this->setElement($elt);
    }


    /**
     * Get the namespace for the element.
     *
     * @return string
     */
    public static function getNamespaceURI(): string
    {
        return static::NS;
    }


    /**
     * Get the namespace-prefix for the element.
     *
     * @return string
     */
    public static function getNamespacePrefix(): string
    {
        return static::NS_PREFIX;
    }


    /**
     * Collect the value of the $element property
     *
     * @return \DOMElement
     */
    public function getElement(): DOMElement
    {
        return $this->element;
    }


    /**
     * Set the value of the elment-property
     *
     * @param \DOMElement $elt
     */
    private function setElement(DOMElement $elt): void
    {
        $this->element = $elt;
    }


    /**
     * Sign the 'Element' and return a 'SignedElement'
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $signingKey  The private key we should use to sign the message
     * @param string[] $certificates  The certificates should be strings with the PEM encoded data
     * @return \SimpleSAML\XMLSecurity\XML\SignedElementInterface
     */
    public function sign(XMLSecurityKey $signingKey, array $certificates = []): SignedElementInterface
    {
        $unsigned = $this->toXML();
        $signature = new Signature($signingKey->getAlgorithm(), $certificates, $signingKey);
        $signedXml = $signature->toXML($this->toXML());
        $signed = new CustomSigned($signedXml, $signature);

        return $signed;
    }


    /**
     * Convert XML into a CustomSignable
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SimpleSAML\XMLSecurity\Test\XML\CustomSignable
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'CustomSignable', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        Assert::minCount($xml->childNodes, 1, MissingElementException::class);
        Assert::maxCount($xml->childNodes, 2, TooManyElementsException::class);

        // Remove the signature
        Signature::getChildrenOfClass($xml);

        $element = $xml->childNodes[0];

        return new self($element);
    }


    /**
     * Convert this CustomSignable to XML.
     *
     * @param \DOMElement|null $element The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this CustomSignable.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        /** @psalm-var \DOMDocument $e->ownerDocument */
        $e = $this->instantiateParentElement($parent);

        $e->appendChild($e->ownerDocument->importNode($this->element, true));
        return $e;
    }
}
