<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Type\PositiveIntegerValue;

use function strval;

/**
 * Class representing a xenc11:IterationCount element.
 *
 * @package simplesamlphp/xml-security
 */
final class IterationCount extends AbstractXenc11Element
{
    /**
     * @param \SimpleSAML\XMLSchema\Type\PositiveIntegerValue $iterationCount
     */
    public function __construct(
        protected PositiveIntegerValue $iterationCount,
    ) {
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\PositiveIntegerValue
     */
    public function getIterationCount(): PositiveIntegerValue
    {
        return $this->iterationCount;
    }


    /**
     * Convert XML into a class instance
     *
     * @param \DOMElement $xml The XML element we should load
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        return new static(
            PositiveIntegerValue::fromString($xml->textContent),
        );
    }


    /**
     * Convert this element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this element to.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getIterationCount());

        return $e;
    }
}
