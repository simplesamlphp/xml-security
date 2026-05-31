<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use Dom;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:Transforms element.
 *
 * @package simplesamlphp/xml-security
 */
final class Transforms extends AbstractDsElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * Initialize a ds:Transforms
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Transform[] $transform
     */
    public function __construct(
        protected array $transform,
    ) {
        Assert::maxCount($transform, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($transform, Transform::class, InvalidArgumentException::class);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\Transform[]
     */
    public function getTransform(): array
    {
        return $this->transform;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     */
    public function isEmptyElement(): bool
    {
        return empty($this->transform);
    }


    /**
     * Convert XML into a Transforms element
     *
     * @param \Dom\Element $xml The XML element we should load
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(Dom\Element $xml): static
    {
        Assert::same($xml->localName, 'Transforms', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Transforms::NS, InvalidDOMElementException::class);

        $transform = Transform::getChildrenOfClass($xml);

        return new static($transform);
    }


    /**
     * Convert this Transforms element to XML.
     *
     * @param \Dom\Element|null $parent The element we should append this Transforms element to.
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getTransform() as $t) {
            if (!$t->isEmptyElement()) {
                $t->toXML($e);
            }
        }

        return $e;
    }
}
