<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;

use function array_last;

/**
 * Class representing a dsig11:ECParameters element.
 *
 * @package simplesaml/xml-security
 */
final class ECParameters extends AbstractECParametersType
{
    /**
     * Convert XML into a ECParameters element
     *
     * @param \DOMElement $xml The XML element we should load
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        $fieldId = FieldID::getChildrenOfClass($xml);
        Assert::minCount($fieldId, 1, MissingElementException::class);
        Assert::maxCount($fieldId, 1, TooManyElementsException::class);

        $curve = Curve::getChildrenOfClass($xml);
        Assert::minCount($curve, 1, MissingElementException::class);
        Assert::maxCount($curve, 1, TooManyElementsException::class);

        $base = Base::getChildrenOfClass($xml);
        Assert::minCount($base, 1, MissingElementException::class);
        Assert::maxCount($base, 1, TooManyElementsException::class);

        $order = Order::getChildrenOfClass($xml);
        Assert::minCount($order, 1, MissingElementException::class);
        Assert::maxCount($order, 1, TooManyElementsException::class);

        $coFactor = CoFactor::getChildrenOfClass($xml);
        Assert::maxCount($coFactor, 1, TooManyElementsException::class);

        $validationData = ValidationData::getChildrenOfClass($xml);
        Assert::maxCount($validationData, 1, TooManyElementsException::class);

        return new static(
            array_last($fieldId),
            array_last($curve),
            array_last($base),
            array_last($order),
            array_last($coFactor),
            array_last($validationData),
        );
    }
}
