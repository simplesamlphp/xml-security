<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use Dom;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSecurity\Assert\Assert;

use function strval;

/**
 * Class representing a ds:SignatureValue element.
 *
 * @package simplesaml/xml-security
 */
final class SignatureValue extends AbstractDsElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * @param \SimpleSAML\XMLSchema\Type\Base64BinaryValue $value
     * @param \SimpleSAML\XMLSchema\Type\IDValue|null $Id
     */
    public function __construct(
        protected Base64BinaryValue $value,
        protected ?IDValue $Id = null,
    ) {
    }


    /**
     * Get the Id used for this signature value.
     *
     * @return \SimpleSAML\XMLSchema\Type\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->Id;
    }


    /**
     * Get the content for this signature value.
     *
     * @return \SimpleSAML\XMLSchema\Type\Base64BinaryValue
     */
    public function getValue(): Base64BinaryValue
    {
        return $this->value;
    }


    /**
     * Convert XML into a SignatureValue element
     *
     * @param \Dom\Element $xml
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(Dom\Element $xml): static
    {
        Assert::same($xml->localName, 'SignatureValue', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureValue::NS, InvalidDOMElementException::class);

        $Id = self::getOptionalAttribute($xml, 'Id', IDValue::class, null);

        return new static(Base64BinaryValue::fromString($xml->textContent), $Id);
    }


    /**
     * Convert this SignatureValue element to XML.
     *
     * @param \Dom\Element|null $parent The element we should append this SignatureValue element to.
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getValue());

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        return $e;
    }
}
