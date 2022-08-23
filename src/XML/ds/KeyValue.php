<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\XMLElementInterface;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:KeyValue element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyValue extends AbstractDsElement
{
    use ExtendableElementTrait;


    /** The namespace-attribute for the xs:any element */
    public const NAMESPACE = C::XS_ANY_NS_OTHER;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue|null $RSAKeyValue */
    protected ?RSAKeyValue $RSAKeyValue;

    // DSA is not supported
    //protected ?DSAKeyValue $DSAKeyValue;


    /**
     * Initialize an KeyValue.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue|null $RSAKeyValue
     * @param \SimpleSAML\XML\XMLElementInterface|null $element
     */
    public function __construct(?RSAKeyValue $RSAKeyValue, ?XMLElementInterface $element = null)
    {
        Assert::false(
            is_null($RSAKeyValue) && is_null($element),
            'A <ds:KeyValue> requires either a RSAKeyValue or an element in namespace ##other',
            SchemaViolationException::class,
        );

        $this->setRSAKeyValue($RSAKeyValue);

        if ($element !== null) {
            $this->setElements([$element]);
        }
    }


    /**
     * Collect the value of the RSAKeyValue-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue|null
     */
    public function getRSAKeyValue(): ?RSAKeyValue
    {
        return $this->RSAKeyValue;
    }


    /**
     * Set the value of the RSAKeyValue-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue $RSAKeyValue
     */
    private function setRSAKeyValue(?RSAKeyValue $RSAKeyValue): void
    {
        $this->RSAKeyValue = $RSAKeyValue;
    }


    /**
     * Convert XML into a KeyValue
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): self
    {
        Assert::same($xml->localName, 'KeyValue', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, KeyValue::NS, InvalidDOMElementException::class);

        $RSAKeyValue = RSAKeyValue::getChildrenOfClass($xml);
        Assert::maxCount(
            $RSAKeyValue,
            1,
            'A <ds:KeyValue> can contain exactly one <ds:RSAKeyValue>',
            TooManyElementsException::class
        );

        $elements = [];
        foreach ($xml->childNodes as $element) {
            if (!($element instanceof DOMElement) || $element->namespaceURI === KeyValue::NS) {
                continue;
            }

            $elements[] = new Chunk($element);
        }
        Assert::maxCount(
            $elements,
            1,
            'A <ds:KeyValue> can contain exactly one element in namespace ##other',
            TooManyElementsException::class
        );

        return new self(array_pop($RSAKeyValue), array_pop($elements));
    }


    /**
     * Convert this KeyValue element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this KeyValue element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->RSAKeyValue !== null) {
            $this->RSAKeyValue->toXML($e);
        }

        foreach ($this->elements as $element) {
            $e->appendChild($e->ownerDocument->importNode($element->toXML(), true));
        }

        return $e;
    }
}
