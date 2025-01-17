<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\XML\Exception\{InvalidDOMElementException, SchemaViolationException};
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\{AnyURIValue, IDValue};
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class representing a dsig11:KeyInfoReference element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfoReference extends AbstractDsig11Element implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Initialize a KeyInfoReference element.
     *
     * @param \SimpleSAML\XML\Type\AnyURIValue $URI
     * @param \SimpleSAML\XML\Type\IDValue|null $Id
     */
    public function __construct(
        protected AnyURIValue $URI,
        protected ?IDValue $Id = null,
    ) {
    }


    /**
     * Collect the value of the URI-property
     *
     * @return \SimpleSAML\XML\Type\AnyURIValue
     */
    public function getURI(): AnyURIValue
    {
        return $this->URI;
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
     * Convert XML into a KeyInfoReference
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'KeyInfoReference', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, KeyInfoReference::NS, InvalidDOMElementException::class);

        return new static(
            KeyInfoReference::getAttribute($xml, 'URI', AnyURIValue::class),
            KeyInfoReference::getOptionalAttribute($xml, 'Id', IDValue::class, null),
        );
    }


    /**
     * Convert this KeyInfoReference element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this KeyInfoReference element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', strval($this->getURI()));

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        return $e;
    }
}
