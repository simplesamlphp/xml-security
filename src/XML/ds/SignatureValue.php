<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class representing a ds:SignatureValue element.
 *
 * @package simplesaml/xml-security
 */
final class SignatureValue extends AbstractDsElement
{
    use XMLStringElementTrait;


    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    private function validateContent(string $content): void
    {
        Assert::notEmpty($content, 'SignatureValue cannot be empty');
        Assert::stringPlausibleBase64($content, 'SignatureValue is not a valid Base64 encoded string');
    }


    /**
     * Convert XML into a SignatureValue
     *
     * @param \DOMElement $xml The XML element we should load
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
}
