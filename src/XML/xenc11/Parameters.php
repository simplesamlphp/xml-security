<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\{ExtendableAttributesTrait, ExtendableElementTrait};
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\XML\Enumeration\NamespaceEnum;

/**
 * Class representing xenc11:Parameters
 *
 * @package simplesamlphp/xml-security
 */
final class Parameters extends AbstractXenc11Element
{
    use ExtendableAttributesTrait;
    use ExtendableElementTrait;

    /** The namespace-attribute for the xs:any element */
    public const XS_ANY_ELT_NAMESPACE = NamespaceEnum::Any;

    /** The namespace-attribute for the xs:anyAttribute element */
    public const XS_ANY_ATTR_NAMESPACE = NamespaceEnum::Any;


    /**
     * Initialize a Parameters element.
     *
     * @param array<\SimpleSAML\XML\SerializableElementInterface> $elements
     * @param array<\SimpleSAML\XML\Attribute> $attributes
     */
    public function __construct(array $elements = [], array $attributes = [])
    {
        $this->setElements($elements);
        $this->setAttributesNS($attributes);
    }



    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     *
     * @return bool
     */
    public function isEmptyElement(): bool
    {
        return empty($this->getAttributesNS())
            && empty($this->getElements());
    }


    /**
     * Convert XML into a Parameters element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Parameters', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Parameters::NS, InvalidDOMElementException::class);

        return new static(
            self::getChildElementsFromXML($xml),
            self::getAttributesNSFromXML($xml),
        );
    }


    /**
     * Convert this Parameters element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Parameters to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getAttributesNS() as $attr) {
            $attr->toXML($e);
        }

        foreach ($this->getElements() as $element) {
            /** @psalm-var \SimpleSAML\XML\SerializableElementInterface $element */
            $element->toXML($e);
        }

        return $e;
    }
}
