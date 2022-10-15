<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\SerializableElementInterface;

/**
 * Class representing a ds:SignatureProperties element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureProperties extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\XML\ds\SignatureProperty[] $signatureProperty */
    protected array $signatureProperty;

    /** @var string|null $Id */
    protected ?string $Id;


    /**
     * Initialize a ds:SignatureProperties
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureProperty[] $signatureProperty
     * @param string $Target
     * @param string|null $Id
     */
    public function __construct(array $signatureProperty, ?string $Id = null)
    {
        $this->setSignatureProperty($signatureProperty);
        $this->setId($Id);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\SignatureProperty[]
     */
    public function getSignatureProperty(): array
    {
        return $this->signatureProperty;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureProperty[] $signatureProperty
     */
    protected function setSignatureProperty(array $signatureProperty): void
    {
        Assert::allIsInstanceOf($signatureProperty, SignatureProperty::class, SchemaViolationException::class);
        $this->signatureProperty = $signatureProperty;
    }


    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->Id;
    }


    /**
     * @param string|null $Id
     */
    private function setId(?string $Id): void
    {
        Assert::nullOrValidNCName($Id);
        $this->Id = $Id;
    }


    /**
     * Convert XML into a SignatureProperties element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SignatureProperties', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureProperties::NS, InvalidDOMElementException::class);

        $signatureProperty = SignatureProperty::getChildrenOfClass($xml);
        $Id = self::getAttribute($xml, 'Id', null);

        Assert::minCount(
            $signatureProperty,
            1,
            'A <ds:SignatureProperties> must contain at least one <ds:SignatureProperty>.',
            MissingElementException::class,
        );

        return new static(
            $signatureProperty,
            $Id,
        );
    }


    /**
     * Convert this SignatureProperties element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignatureProperties element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getId() !== null) {
            $e->setAttribute('Id', $this->getId());
        }

        foreach ($this->getSignatureProperty() as $signatureProperty) {
            $signatureProperty->toXML($e);
        }

        return $e;
    }
}
