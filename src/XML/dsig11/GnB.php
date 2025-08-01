<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};

use function array_pop;

/**
 * Class representing a dsig11:GnB element.
 *
 * @package simplesaml/xml-security
 */
final class GnB extends AbstractCharTwoFieldParamsType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Convert XML into a GnB element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        $m = M::getChildrenOfClass($xml);
        Assert::minCount($m, 1, MissingElementException::class);
        Assert::maxCount($m, 1, TooManyElementsException::class);

        return new static(
            array_pop($m),
        );
    }
}
