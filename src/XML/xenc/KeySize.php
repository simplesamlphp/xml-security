<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class representing a xenc:KeySize element.
 *
 * @package simplesaml/xml-security
 * @psalm-suppress PropertyNotSetInConstructor $content
 */
final class KeySize extends AbstractXencElement
{
    /** @var int */
    protected int $keySize;


    /**
     * @param int $keySize
     */
    public function __construct(int $keySize)
    {
        $this->setKeySize($keySize);
    }


    /**
     * @param int $keySize
     */
    protected function setKeySize(int $keySize): void
    {
        Assert::positiveInteger($keySize, SchemaViolationException::class);
        $this->keySize = $keySize;
    }


    /**
     * @return int
     */
    public function getKeySize(): int
    {
        return $this->keySize;
    }


    /**
     * Convert XML into a class instance
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);
        Assert::numeric($xml->textContent);

        return new static(intval($xml->textContent));
    }


    /**
     * Convert this element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getKeySize());

        return $e;
    }
}
