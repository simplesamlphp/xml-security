<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:KeyInfoReference element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfoReference extends AbstractDsElement
{
    /** @var string $URI */
    protected string $URI;

    /** @var string|null $Id */
    protected ?string $Id = null;


    /**
     * Initialize a KeyInfoReference element.
     *
     * @param string $URI
     * @param string|null $Id
     */
    public function __construct(string $URI, ?string $Id = null)
    {
        $this->setURI($URI);
        $this->setId($Id);
    }


    /**
     * Collect the value of the URI-property
     *
     * @return string
     */
    public function getURI(): string
    {
        return $this->URI;
    }


    /**
     * Set the value of the URI-property
     *
     * @param string $URI
     */
    private function setURI(string $URI): void
    {
        Assert::validURI($URI, SchemaViolationException::class);
        $this->URI = $URI;
    }


    /**
     * Collect the value of the Id-property
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->Id;
    }


    /**
     * Set the value of the Id-property
     *
     * @param string $Id
     */
    private function setId(?string $Id): void
    {
        $this->Id = $Id;
    }


    /**
     * Convert XML into a KeyInfoReference
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): self
    {
        Assert::same($xml->localName, 'KeyInfoReference', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, KeyInfoReference::NS, InvalidDOMElementException::class);

        /** @psalm-var string $URI */
        $URI = KeyInfoReference::getAttribute($xml, 'URI');
        $Id = KeyInfoReference::getAttribute($xml, 'Id', null);

        return new self($URI, $Id);
    }


    /**
     * Convert this KeyInfoReference element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this KeyInfoReference element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', $this->URI);

        if ($this->Id !== null) {
            $e->setAttribute('Id', $this->Id);
        }

        return $e;
    }
}
