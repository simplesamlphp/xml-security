<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\XML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue};
use SimpleSAML\XML\TypedTextContentTrait;

use function strval;

/**
 * Class representing a dsig11:DEREncodedKeyValue element.
 *
 * @package simplesaml/xml-security
 */
final class DEREncodedKeyValue extends AbstractDsig11Element implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use TypedTextContentTrait;


    /**
     * Initialize a DEREncodedKeyValue element.
     *
     * @param \SimpleSAML\XML\Type\Base64BinaryValue $value
     * @param \SimpleSAML\XML\Type\IDValue|null $Id
     */
    public function __construct(
        protected Base64BinaryValue $value,
        protected ?IDValue $Id = null,
    ) {
    }


    /**
     * Get the content for this signature value.
     *
     * @return \SimpleSAML\XML\Type\Base64BinaryValue
     */
    public function getValue(): ?Base64BinaryValue
    {
        return $this->value;
    }


    /**
     * Collect the value of the Id-property
     *
     * @return \SimpleSAML\XML\Type\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->Id;
    }


    /**
     * Convert XML into a DEREncodedKeyValue
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
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        return new static(
            Base64BinaryValue::fromString($xml->textContent),
            self::getOptionalAttribute($xml, 'Id', IDValue::class, null),
        );
    }


    /**
     * Convert this DEREncodedKeyValue element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this DEREncodedKeyValue element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getValue());

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        return $e;
    }
}
