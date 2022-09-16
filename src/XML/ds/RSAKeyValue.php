<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:RSAKeyValue element.
 *
 * @package simplesamlphp/xml-security
 */
final class RSAKeyValue extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\XML\ds\Modulus $modulus */
    protected Modulus $modulus;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\Exponent $exponent */
    protected Exponent $exponent;


    /**
     * Initialize an RSAKeyValue.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Modulus $modulus
     * @param \SimpleSAML\XMLSecurity\XML\ds\Exponent $exponent
     */
    final public function __construct(Modulus $modulus, Exponent $exponent)
    {
        $this->setModulus($modulus);
        $this->setExponent($exponent);
    }


    /**
     * Collect the value of the modulus-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\Modulus
     */
    public function getModulus(): Modulus
    {
        return $this->modulus;
    }


    /**
     * Set the value of the modulus-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Modulus $modulus
     */
    private function setModulus(Modulus $modulus): void
    {
        $this->modulus = $modulus;
    }


    /**
     * Collect the value of the exponent-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\Exponent
     */
    public function getExponent(): Exponent
    {
        return $this->exponent;
    }


    /**
     * Set the value of the exponent-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Exponent $exponent
     */
    private function setExponent(Exponent $exponent): void
    {
        $this->exponent = $exponent;
    }


    /**
     * Convert XML into a RSAKeyValue
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'RSAKeyValue', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, RSAKeyValue::NS, InvalidDOMElementException::class);

        $modulus = Modulus::getChildrenOfClass($xml);
        Assert::minCount(
            $modulus,
            1,
            'An <ds:RSAKeyValue> must contain exactly one <ds:Modulus>',
            MissingElementException::class
        );
        Assert::maxCount(
            $modulus,
            1,
            'An <ds:RSAKeyValue> must contain exactly one <ds:Modulus>',
            TooManyElementsException::class
        );

        $exponent = Exponent::getChildrenOfClass($xml);
        Assert::minCount(
            $exponent,
            1,
            'An <ds:RSAKeyValue> must contain exactly one <ds:Modulus>',
            MissingElementException::class
        );
        Assert::maxCount(
            $exponent,
            1,
            'An <ds:RSAKeyValue> must contain exactly one <ds:Modulus>',
            TooManyElementsException::class
        );

        return new static(array_pop($modulus), array_pop($exponent));
    }


    /**
     * Convert this RSAKeyValue element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this RSAKeyValue element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->modulus->toXML($e);
        $this->exponent->toXML($e);

        return $e;
    }
}
