<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};
use SimpleSAML\XMLSchema\Type\AnyURIValue;

use function array_pop;

/**
 * Class representing a dsig11:ValidationData element.
 *
 * @package simplesaml/xml-security
 */
final class ValidationData extends AbstractECValidationDataType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Convert XML into a ValidationData element
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

        $seed = Seed::getChildrenOfClass($xml);
        Assert::minCount($seed, 1, MissingElementException::class);
        Assert::maxCount($seed, 1, TooManyElementsException::class);

        return new static(
            array_pop($seed),
            self::getAttribute($xml, 'hashAlgorithm', AnyURIValue::class),
        );
    }
}
