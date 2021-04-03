<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:Transform element.
 *
 * @package simplesamlphp/xml-security
 */
final class Transform extends AbstractDsElement
{
    /** @var string */
    protected string $Algorithm;

    /** @var \SimpleSAML\XML\Chunk[] */
    protected array $elements = [];


    /**
     * Initialize a ds:Transform
     *
     * @param string $Algorithm
     * @param \SimpleSAML\XML\Chunk[] $elements
     */
    public function __construct(
        string $Algorithm,
        array $elements = []
    ) {
        $this->setElements($elements);
        $this->setAlgorithm($Algorithm);
    }


    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->Algorithm;
    }


    /**
     * @param string $Algorithm
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    protected function setAlgorithm(string $Algorithm): void
    {
        Assert::notEmpty($Algorithm, 'Cannot set an empty algorithm in ' . static::NS_PREFIX . ':Transform.');
        $this->Algorithm = $Algorithm;
    }


    /**
     * Collect the elements
     *
     * @return \SimpleSAML\XML\Chunk[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }


    /**
     * Set the value of the elements-property
     *
     * @param \SimpleSAML\XML\Chunk[] $elements
     * @throws \SimpleSAML\Assert\AssertionFailedException if the supplied array contains anything other than Chunk objects
     */
    private function setElements(array $elements): void
    {
        Assert::allIsInstanceOf($elements, Chunk::class);

        $this->elements = $elements;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     *
     * @return bool
     */
    public function isEmptyElement(): bool
    {
        return empty($this->elements);
    }


    /**
     * Convert XML into a Transform element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'Transform', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Transform::NS, InvalidDOMElementException::class);

        $Algorithm = self::getAttribute($xml, 'Algorithm');

        $elements = [];
        foreach ($xml->childNodes as $element) {
            if (!($element instanceof DOMElement)) {
                continue;
            }

            $elements[] = new Chunk($element);
        }

        return new self($Algorithm, $elements);
    }


    /**
     * Convert this Transform element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Transform element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', $this->Algorithm);

        foreach ($this->elements as $element) {
            $e->appendChild($e->ownerDocument->importNode($element->getXML(), true));
        }

        return $e;
    }
}
