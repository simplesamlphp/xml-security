<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class representing a ds:KeyName element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyName extends AbstractDsElement
{
    use XMLStringElementTrait;


    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    protected function validateContent(string $content): void
    {
        /**
         * Perform no validation by default.
         * Override this method on the implementing class to perform content validation.
         */
    }


    /**
     * Convert XML into a KeyName
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'KeyName', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, KeyName::NS, InvalidDOMElementException::class);

        return new self($xml->textContent);
    }
}
