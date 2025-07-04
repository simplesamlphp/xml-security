<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class representing a dsig11:K3 element.
 *
 * @package simplesaml/xml-security
 */
final class K3 extends AbstractDsig11Element
{
    /**
     * @param int $k3
     */
    public function __construct(
        protected int $k3,
    ) {
        Assert::positiveInteger($k3, SchemaViolationException::class);
    }


    /**
     * @return int
     */
    public function getK3(): int
    {
        return $this->k3;
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
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getK3());

        return $e;
    }
}
