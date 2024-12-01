<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\Registry\ElementRegistry;
use SimpleSAML\XML\SerializableElementInterface;
use SimpleSAML\XML\XsNamespace as NS;
use SimpleSAML\XMLSecurity\XML\ds\AbstractDsElement;

/**
 * Abstract class representing the SPKIDataType.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractSPKIDataType extends AbstractDsElement
{
    /**
     * Initialize a SPKIData element.
     *
     * @param array<\SimpleSAML\XMLSecurity\XML\ds\SPKISexp, SimpleSAML\XML\SerializableElementInterface|null> $tuples
     */
    final public function __construct(
        protected array $tuples,
    ) {
        Assert::allIsArray($tuples, SchemaViolationException::class);
        Assert::allCount($tuples, 2);

        foreach ($tuples as $tuple) {
            list($spkisExp, $other) = $tuple;
            Assert::isInstanceOf($spkisExp, SPKISexp::class, SchemaViolationException::class);
            Assert::nullOrIsInstanceOf($other, SerializableElementInterface::class, SchemaViolationException::class);
        }
    }


    /**
     * Collect the value of the SPKISexp-property
     *
     * @return array<\SimpleSAML\XMLSecurity\XML\ds\SPKISexp, SimpleSAML\XML\SerializableElementInterface|null>
     */
    public function getTuples(): array
    {
        return $this->tuples;
    }


    /**
     * Convert XML into a SPKIData
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
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $registry = ElementRegistry::getInstance();
        $tuples = [];
        $tuple = [null, null];
        foreach ($xml->childNodes as $node) {
            if ($node instanceof DOMElement) {
                if ($node->namespaceURI === static::NS && $node->localName === 'SPKISexp') {
                    if ($tuple[0] !== null) {
                        $tuples[] = $tuple;
                    }
                    $tuple = [SPKISexp::fromXML($node), null];
                } elseif ($node->namespaceURI !== static::NS && $tuple[0] !== null) {
                    $handler = $registry->getElementHandler($node->namespaceURI, $node->localName);
                    $tuple[1] = ($handler === null) ? Chunk::fromXML($node) : $handler::fromXML($node);
                    $tuples[] = $tuple;
                    $tuple = [null, null];
                }
            }
        }

        if ($tuple[0] !== null) {
            $tuples[] = $tuple;
        }

        return new static($tuples);
    }


    /**
     * Convert this SPKIData to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SPKIData to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getTuples() as $tuple) {
            list($spkisExp, $other) = $tuple;

            $spkisExp->toXML($e);
            $other?->toXML($e);
        }

        return $e;
    }
}
