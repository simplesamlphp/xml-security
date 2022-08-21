<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;

/**
 * Class representing a ds:Manifest element.
 *
 * @package simplesamlphp/xml-security
 */
final class Manifest extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\XML\ds\Reference[] */
    protected array $references;

    /** @var string|null $Id */
    protected ?string $Id;


    /**
     * Initialize a ds:Manifest
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Reference[] $references
     * @param string|null $Id
     */
    public function __construct(
        array $references,
        ?string $Id = null
    ) {
        $this->setReferences($references);
        $this->setId($Id);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\Reference[]
     */
    public function getReferences(): array
    {
        return $this->references;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\Reference[] $references
     */
    protected function setReferences(array $references): void
    {
        $this->references = $references;
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
        $this->Id = $Id;
    }


    /**
     * Convert XML into a Manifest element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): self
    {
        Assert::same($xml->localName, 'Manifest', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Manifest::NS, InvalidDOMElementException::class);

        $Id = self::getAttribute($xml, 'Id', null);

        /** @psalm-var \SimpleSAML\XMLSecurity\XML\ds\Reference[] $references */
        $references = Reference::getChildrenOfClass($xml);
        Assert::minCount(
            $references,
            1,
            'A <ds:Manifest> must contain at least one <ds:Reference>.',
            MissingElementException::class,
        );

        return new self(
            $references,
            $Id,
        );
    }


    /**
     * Convert this Manifest element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Manifest element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->Id !== null) {
            $e->setAttribute('Id', $this->Id);
        }

        foreach ($this->references as $reference) {
            $reference->toXML($e);
        }

        return $e;
    }
}
