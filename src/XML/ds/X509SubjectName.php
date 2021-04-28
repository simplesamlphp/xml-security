<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class representing a ds:X509SubjectName element.
 *
 * @package simplesaml/xml-security
 */
final class X509SubjectName extends AbstractDsElement
{
    use XMLStringElementTrait;


    /**
     * Convert XML into a X509SubjectName
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'X509SubjectName', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, X509SubjectName::NS, InvalidDOMElementException::class);

        return new self($xml->textContent);
    }
}
