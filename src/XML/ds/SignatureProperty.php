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
use SimpleSAML\XML\XMLElementInterface;

/**
 * Class representing a ds:SignatureProperty element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureProperty extends AbstractDsElement
{
    use ExtendableElementTrait;


    /** The namespace-attribute for the xs:any element */
    public const NAMESPACE = C::XS_ANY_NS_OTHER;

    /** @var string $Target */
    protected string $Target;

    /** @var string|null $Id */
    protected ?string $Id;


    /**
     * Initialize a ds:SignatureProperty
     *
     * @param \SimpleSAML\XML\XMLElementInterface[] $elements
     * @param string $Target
     * @param string|null $Id
     */
    public function __construct(
        array $elements,
        string $Target,
        ?string $Id = null
    ) {
        $this->setElements($elements);
        $this->setTarget($Target);
        $this->setId($Id);
    }


    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->Target;
    }


    /**
     * @param string $Target
     */
    protected function setTarget(string $Target): void
    {
        Assert::validURI($Target, SchemaViolationException::class); // Covers the empty string
        $this->Target = $Target;
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
     * Convert XML into a SignatureProperty element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): self
    {
        Assert::same($xml->localName, 'SignatureProperty', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureProperty::NS, InvalidDOMElementException::class);

        /** @psalm-var string $Target */
        $Target = self::getAttribute($xml, 'Target');
        $Id = self::getAttribute($xml, 'Id', null);

        $children = [];
        foreach ($xml->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }

            $children[] = new Chunk($child);
        }

        /** @psalm-var \SimpleSAML\XML\XMLElementInterface[] $children */
        Assert::minCount(
            $children,
            1,
            'A <ds:SignatureProperty> must contain at least one element.',
            MissingElementException::class,
        );

        return new self(
            $children,
            $Target,
            $Id,
        );
    }


    /**
     * Convert this SignatureProperty element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignatureProperty element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Target', $this->Target);

        if ($this->Id !== null) {
            $e->setAttribute('Id', $this->Id);
        }

        foreach ($this->elements as $element) {
            $e->appendChild($e->ownerDocument->importNode($element->getXML(), true));
        }

        return $e;
    }
}
