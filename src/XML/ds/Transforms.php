<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:Transforms element.
 *
 * @package simplesamlphp/xml-security
 */
final class Transforms extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\Transform[] */
    protected array $transform;


    /**
     * Initialize a ds:Transforms
     *
     * @param \SimpleSAML\XMLSecurity\Transform[] $transform
     */
    public function __construct(array $transform)
    {
        $this->setTransform($transform);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\Transform[]
     */
    public function getTransform(): array
    {
        return $this->transform;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\Transform[] $transform
     */
    protected function setTransform(array $transform): void
    {
        Assert::allIsInstanceOf($transform, Transform::class);
        $this->transform = $transform;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     *
     * @return bool
     */
    public function isEmptyElement(): bool
    {
        return empty($this->transform);
    }


    /**
     * Convert XML into a Transforms element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'Transforms', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Transforms::NS, InvalidDOMElementException::class);

        $transform = Transform::getChildrenOfClass($xml);

        return new self($transform);
    }


    /**
     * Convert this Transforms element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Transforms element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->transform as $t) {
            if (!$t->isEmptyElement()) {
                $t->toXML($e);
            }
        }

        return $e;
    }
}
