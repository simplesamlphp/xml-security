<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;

/**
 * Class representing a ds:RetrievalMethod element.
 *
 * @package simplesamlphp/xml-security
 */
final class RetrievalMethod extends AbstractDsElement
{
    protected ?Transforms $transforms;

    /** @var string $URI */
    protected string $URI;

    /** @var string|null $Type */
    protected ?string $Type;


    /**
     * Initialize a ds:RetrievalMethod
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Transforms|null $transforms
     * @param string $URI
     * @param string|null $Type
     */
    public function __construct(
        ?Transforms $transforms,
        string $URI,
        ?string $Type = null
    ) {
        $this->setTransforms($transforms);
        $this->setURI($URI);
        $this->setType($Type);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\Transforms|null
     */
    public function getTransforms(): ?Transforms
    {
        return $this->transforms;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\Transforms|null $transforms
     */
    protected function setTransforms(?Transforms $transforms): void
    {
        $this->transforms = $transforms;
    }


    /**
     * @return string
     */
    public function getURI(): string
    {
        return $this->URI;
    }


    /**
     * @param string $URI
     */
    private function setURI(string $URI): void
    {
        Assert::validURI($URI, SchemaViolationException::class); // Covers the empty string
        $this->URI = $URI;
    }


    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->Type;
    }


    /**
     * @param string|null $Type
     */
    private function setType(?string $Type): void
    {
        Assert::validURI($Type, SchemaViolationException::class); // Covers the empty string
        $this->Type = $Type;
    }


    /**
     * Convert XML into a RetrievalMethod element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): self
    {
        Assert::same($xml->localName, 'RetrievalMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, RetrievalMethod::NS, InvalidDOMElementException::class);

        /** @psalm-var string $URI */
        $URI = self::getAttribute($xml, 'URI');
        $Type = self::getAttribute($xml, 'Type', null);

        /** @psalm-var \SimpleSAML\XMLSecurity\XML\ds\Transforms[] $transforms */
        $transforms = Transforms::getChildrenOfClass($xml);
        Assert::maxCount(
            $transforms,
            1,
            'A <ds:RetrievalMethod> may contain a maximum of one <ds:Transforms>.',
            TooManyElementsException::class,
        );

        return new self(
            array_pop($transforms),
            $URI,
            $Type
        );
    }


    /**
     * Convert this RetrievalMethod element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this RetrievalMethod element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', $this->URI);

        if ($this->Type !== null) {
            $e->setAttribute('Type', $this->Type);
        }

        if ($this->transforms !== null) {
            $this->transforms->toXML($e);
        }

        return $e;
    }
}
