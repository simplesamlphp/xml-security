<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:SignatureMethod element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureMethod extends AbstractDsElement
{
    /**
     * The algorithm.
     *
     * @var string
     */
    protected string $Algorithm;


    /**
     * Initialize a SignatureMethod element.
     *
     * @param string $algorithm
     */
    public function __construct(string $algorithm)
    {
        $this->setAlgorithm($algorithm);
    }


    /**
     * Collect the value of the Algorithm-property
     *
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->Algorithm;
    }


    /**
     * Set the value of the Algorithm-property
     *
     * @param string $algorithm
     */
    private function setAlgorithm(string $algorithm): void
    {
        Assert::oneOf(
            $algorithm,
            array_merge(
                array_keys(C::$RSA_DIGESTS),
                array_keys(C::$HMAC_DIGESTS)
            ),
            'Invalid signature method',
            InvalidArgumentException::class
        );

        $this->Algorithm = $algorithm;
    }


    /**
     * Convert XML into a SignatureMethod
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'SignatureMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureMethod::NS, InvalidDOMElementException::class);

        $Algorithm = SignatureMethod::getAttribute($xml, 'Algorithm');

        return new self($Algorithm);
    }


    /**
     * Convert this SignatureMethod element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignatureMethod element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', $this->Algorithm);

        return $e;
    }
}
