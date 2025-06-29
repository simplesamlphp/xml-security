<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, SchemaViolationException};
use SimpleSAML\XMLSchema\Type\Builtin\{AnyURIValue, IDValue};
use SimpleSAML\XMLSchema\XML\xs\NamespaceEnum;
use SimpleSAML\XMLSecurity\Assert\Assert;

use function strval;

/**
 * Class representing a ds:SignatureProperty element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureProperty extends AbstractDsElement implements SchemaValidatableElementInterface
{
    use ExtendableElementTrait;
    use SchemaValidatableElementTrait;

    /** The namespace-attribute for the xs:any element */
    public const XS_ANY_ELT_NAMESPACE = NamespaceEnum::Other;


    /**
     * Initialize a ds:SignatureProperty
     *
     * @param \SimpleSAML\XML\SerializableElementInterface[] $elements
     * @param \SimpleSAML\XMLSchema\Type\Builtin\AnyURIValue $Target
     * @param \SimpleSAML\XMLSchema\Type\Builtin\IDValue|null $Id
     */
    public function __construct(
        array $elements,
        protected AnyURIValue $Target,
        protected ?IDValue $Id = null,
    ) {
        $this->setElements($elements);
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\Builtin\AnyURIValue
     */
    public function getTarget(): AnyURIValue
    {
        return $this->Target;
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\Builtin\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->Id;
    }


    /**
     * Convert XML into a SignatureProperty element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SignatureProperty', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureProperty::NS, InvalidDOMElementException::class);

        $children = self::getChildElementsFromXML($xml);
        Assert::minCount(
            $children,
            1,
            'A <ds:SignatureProperty> must contain at least one element.',
            MissingElementException::class,
        );

        return new static(
            $children,
            self::getAttribute($xml, 'Target', AnyURIValue::class),
            self::getOptionalAttribute($xml, 'Id', IDValue::class, null),
        );
    }


    /**
     * Convert this SignatureProperty element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignatureProperty element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Target', strval($this->getTarget()));

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        foreach ($this->getElements() as $element) {
            $element->toXML($e);
        }

        return $e;
    }
}
