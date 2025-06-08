<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a dsig11:FieldID element.
 *
 * @package simplesaml/xml-security
 */
final class FieldID extends AbstractFieldIDType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Convert XML into a FieldID element
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
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        $prime = Prime::getChildrenOfClass($xml);
        Assert::minCount($prime, 1, MissingElementException::class);
        Assert::maxCount($prime, 1, TooManyElementsException::class);

        $tnb = TnB::getChildrenOfClass($xml);
        Assert::minCount($tnb, 1, MissingElementException::class);
        Assert::maxCount($tnb, 1, TooManyElementsException::class);

        $pnb = PnB::getChildrenOfClass($xml);
        Assert::minCount($pnb, 1, MissingElementException::class);
        Assert::maxCount($pnb, 1, TooManyElementsException::class);

        $gnb = GnB::getChildrenOfClass($xml);
        Assert::minCount($gnb, 1, MissingElementException::class);
        Assert::maxCount($gnb, 1, TooManyElementsException::class);

        return new static(
            array_pop($prime),
            array_pop($tnb),
            array_pop($pnb),
            array_pop($gnb),
            self::getChildElementsFromXML($xml),
        );
    }
}
