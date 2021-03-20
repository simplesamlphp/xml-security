<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:X509SubjectName element.
 *
 * @package simplesaml/xml-security
 */
final class X509SubjectName extends AbstractDsElement
{
    /**
     * The subject name.
     *
     * @var string
     */
    protected string $name;


    /**
     * Initialize a X509SubjectName element.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }


    /**
     * Collect the value of the name-property
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Set the value of the name-property
     *
     * @param string $name
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }


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


    /**
     * Convert this X509SubjectName element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this X509SubjectName element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->name;

        return $e;
    }
}