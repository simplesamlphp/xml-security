<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:SignatureValue element.
 *
 * @package simplesaml/xml-security
 */
final class SignatureValue extends AbstractDsElement
{
    /** @var string */
    protected string $value;


    /**
     * Initialize a SignatureValue element.
     *
     * @param string $value
     */
    public function __construct(string $value) {
        $this->setValue($value);
    }


    /**
     * Get the signature value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }


    /**
     * Set the value.
     *
     * @param string $value
     */
    private function setValue(string $value): void
    {
        Assert::notEmpty($value, 'SignatureValue cannot be empty');
        Assert::stringPlausibleBase64($value, 'SignatureValue is not a valid Base64 encoded string');
        $this->value = $value;
    }


    /**
     * Convert XML into a SignatureValue
     *
     * @param DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException If the qualified name of the supplied element is
     * wrong.
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'SignatureValue', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureValue::NS, InvalidDOMElementException::class);

        return new self($xml->textContent);
    }


    /**
     * Convert this SignatureValue to XML.
     *
     * @param DOMElement|null $parent The element we should append this SignatureValue element to.
     * @return DOMElement
     */
    public function toXML(DOMElement $parent = null): \DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->value;

        return $e;
    }
}