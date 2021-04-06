<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\XMLElementInterface;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

/**
 * Abstract class representing references. No custom elements are allowed.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractReference extends AbstractXencElement
{
    /** @var string */
    protected string $uri;

    /** @var \SimpleSAML\XML\XMLElementInterface[] */
    protected array $elements;


    /**
     * AbstractReference constructor.
     *
     * @param string $uri
     * @param \SimpleSAML\XML\XMLElementInterface[] $elements
     */
    protected function __construct(string $uri, array $elements = [])
    {
        $this->setURI($uri);
        $this->setElements($elements);
    }


    /**
     * Get the value of the URI attribute of this reference.
     *
     * @return string
     */
    public function getURI(): string
    {
        return $this->uri;
    }


    /**
     * @param string $uri
     */
    protected function setURI(string $uri): void
    {
        Assert::notEmpty($uri, 'The URI attribute of a reference cannot be empty.');
        $this->uri = $uri;
    }


    /**
     * Collect the embedded elements
     *
     * @return \SimpleSAML\XML\XMLElementInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }


    /**
     * Set the value of the elements-property
     *
     * @param \SimpleSAML\XML\XMLElementInterface[] $elements
     * @throws \SimpleSAML\Assert\AssertionFailedException
     *   if the supplied array contains anything other than XMLElementInterface objects
     */
    private function setElements(array $elements): void
    {
        Assert::allIsInstanceOf($elements, XMLElementInterface::class);
        $this->elements = $elements;
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XML\Exception\MissingAttributeException
     *   if the supplied element is missing one of the mandatory attributes
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, static::getClassName(static::class), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $URI = self::getAttribute($xml, 'URI');

        $elements = [];
        foreach ($xml->childNodes as $element) {
            if (!($element instanceof DOMElement)) {
                continue;
            } elseif ($element->namespaceURI === Transforms::NS && $element->localName === 'Transforms') {
                $elements[] = Transforms::fromXML($element);
            } else {
                $elements[] = new Chunk($element);
            }
        }

        return new static($URI, $elements);
    }


    /**
     * @inheritDoc
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', $this->uri);

        foreach ($this->elements as $element) {
            $element->toXML($e);
        }

        return $e;
    }
}
