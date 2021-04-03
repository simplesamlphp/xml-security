<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:DigestValue element.
 *
 * @package simplesaml/xml-security
 */
final class DigestValue extends AbstractDsElement
{
    /**
     * The digest value.
     *
     * @var string
     */
    protected string $digest;


    /**
     * Initialize a DigestValue element.
     *
     * @param string $digest
     */
    public function __construct(string $digest)
    {
        $this->setDigest($digest);
    }


    /**
     * Collect the value of the digest-property
     *
     * @return string
     */
    public function getDigest(): string
    {
        return $this->digest;
    }


    /**
     * Set the value of the digest-property
     *
     * @param string $digest
     */
    private function setDigest(string $digest): void
    {
        Assert::stringPlausibleBase64($digest, 'ds:DigestValue is not a valid Base64 encoded string');
        $this->digest = $digest;
    }


    /**
     * Convert XML into a DigestValue
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'DigestValue', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, DigestValue::NS, InvalidDOMElementException::class);

        return new self($xml->textContent);
    }


    /**
     * Convert this DigestValue element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this DigestValue element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->digest;

        return $e;
    }
}
