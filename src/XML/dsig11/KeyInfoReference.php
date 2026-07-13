<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use Dom;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\IDValue;
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
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue $URI
     * @param \SimpleSAML\XMLSchema\Type\IDValue|null $Id
     */
    public function __construct(
        protected AnyURIValue $URI,
        protected ?IDValue $Id = null,
    ) {
    }


    /**
     * Collect the value of the URI-property
     *
     * @return \SimpleSAML\XMLSchema\Type\AnyURIValue
     */
    public function getURI(): AnyURIValue
    {
        return $this->URI;
    }


    /**
     * Collect the value of the Id-property
     *
     * @return \SimpleSAML\XMLSchema\Type\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->Id;
    }


    /**
     * Convert XML into a KeyInfoReference
     *
     * @param \Dom\Element $xml The XML element we should load
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(Dom\Element $xml): static
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
     * @param \Dom\Element|null $parent The element we should append this KeyInfoReference element to.
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', strval($this->getURI()));

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        return $e;
    }
}
