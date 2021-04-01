<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:CanonicalizationMethod element.
 *
 * @package simplesamlphp/xml-security
 */
final class CanonicalizationMethod extends AbstractDsElement
{
    /**
     * The algorithm.
     *
     * @var string
     */
    protected string $Algorithm;


    /**
     * Initialize a CanonicalizationMethod element.
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
            [
                Constants::C14N_EXCLUSIVE_WITH_COMMENTS,
                Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
                Constants::C14N_INCLUSIVE_WITH_COMMENTS,
                Constants::C14N_INCLUSIVE_WITHOUT_COMMENTS
            ],
            'Invalid canonicalization method',
            InvalidArgumentException::class
        );

        $this->Algorithm = $algorithm;
    }


    /**
     * Convert XML into a CanonicalizationMethod
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'CanonicalizationMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, CanonicalizationMethod::NS, InvalidDOMElementException::class);

        $Algorithm = CanonicalizationMethod::getAttribute($xml, 'Algorithm');

        return new self($Algorithm);
    }


    /**
     * Convert this CanonicalizationMethod element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this KeyName element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', $this->Algorithm);

        return $e;
    }
}
