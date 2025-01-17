<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Type\StringValue;

use function strval;

/**
 * Class implementing the XPath element.
 *
 * @package simplesamlphp/xml-security
 */
class XPath extends AbstractDsElement
{
    /**
     * Construct an XPath element.
     *
     * @param \SimpleSAML\XML\Type\StringValue $expression The XPath expression itself.
     */
    final public function __construct(
        protected StringValue $expression,
    ) {
    }


    /**
     * Get the actual XPath expression.
     *
     * @return \SimpleSAML\XML\Type\StringValue
     */
    public function getExpression(): StringValue
    {
        return $this->expression;
    }


    /**
     * Convert XML into a class instance
     *
     * @param DOMElement $xml
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'XPath', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, self::NS, InvalidDOMElementException::class);

        return new static(StringValue::fromString($xml->textContent));
    }


    /**
     * @param DOMElement|null $parent
     * @return DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getExpression());

        return $e;
    }
}
