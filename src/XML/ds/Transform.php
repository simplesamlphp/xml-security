<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces;
use Webmozart\Assert\Assert;

/**
 * Class representing transforms.
 *
 * @package simplesamlphp/xml-security
 */
class Transform extends AbstractDsElement
{
    /**
     * The algorithm used for this transform.
     *
     * @var string
     */
    protected string $algorithm;

    /**
     * An XPath object.
     *
     * @var XPath|null
     */
    protected XPath $xpath;

    /**
     * An InclusiveNamespaces object.
     *
     * @var InclusiveNamespaces|null
     */
    protected InclusiveNamespaces $inclusiveNamespaces;


    /**
     * Initialize the Transform element.
     *
     * @param string $algorithm
     * @param XPath|null $xpath
     * @param InclusiveNamespaces|null $prefixes
     */
    public function __construct(
        string $algorithm,
        ?XPath $xpath,
        ?InclusiveNamespaces $inclusiveNamespaces
    ) {
        $this->setAlgorithm($algorithm);
        $this->setXPath($xpath);
        $this->setInclusiveNamespaces($inclusiveNamespaces);
    }


    /**
     * Get the algorithm associated with this transform.
     *
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }


    /**
     * Set the value of the algorithm property.
     *
     * @param string $algorithm
     */
    private function setAlgorithm(string $algorithm): void
    {
        Assert::oneOf(
            $algorithm,
            [
                C::C14N_EXCLUSIVE_WITH_COMMENTS,
                C::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
                C::C14N_INCLUSIVE_WITH_COMMENTS,
                C::C14N_INCLUSIVE_WITHOUT_COMMENTS
            ],
            'Unsupported Transform algorithm.'
        );
    }


    /**
     * Get the XPath associated with this transform.
     *
     * @return XPath|null
     */
    public function getXPath(): XPath
    {
        return $this->xpath;
    }


    /**
     * Set and validate the XPath object.
     *
     * @param XPath|null $XPath
     */
    private function setXPath(?XPath $xpath): void
    {
        if ($xpath === null) {
            return;
        }
        Assert::eq(
            $this->algorithm,
            C::XPATH_URI,
            'Transform algorithm "' . C::XPATH_URI . '" required if XPath provided.'
        );
        $this->xpath = $xpath;
    }


    /**
     * Get the InclusiveNamespaces associated with this transform.
     *
     * @return InclusiveNamespaces|null
     */
    public function getInclusiveNamespaces(): array
    {
        return $this->inclusiveNamespaces;
    }


    /**
     * Set and validate the InclusiveNamespaces object.
     *
     * @param InclusiveNamespaces|null $inclusiveNamespaces
     */
    private function setInclusiveNamespaces(?InclusiveNamespaces $inclusiveNamespaces)
    {
        if ($inclusiveNamespaces === null) {
            return;
        }
        Assert::oneOf(
            $this->algorithm,
            [
                C::C14N_INCLUSIVE_WITH_COMMENTS,
                C::C14N_EXCLUSIVE_WITHOUT_COMMENTS
            ],
            'Transform algorithm "' . C::C14N_EXCLUSIVE_WITH_COMMENTS . '" or "' .
            C::C14N_EXCLUSIVE_WITHOUT_COMMENTS . '" required if InclusiveNamespaces provided.'
        );
        $this->inclusiveNamespaces = $inclusiveNamespaces;
    }


    /**
     * Convert XML into a Transform element.
     *
     * @param \DOMElement $xml The XML element we should load.
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        $alg = self::getAttribute($xml, 'Algorithm');
        $xpath = XPath::getChildrenOfClass($xml);
        Assert::maxCount($xpath, 1, 'Only one XPath element supported per Transform.');
        $prefixes = InclusiveNamespaces::getChildrenOfClass($xml);
        Assert::maxCount(
            $prefixes,
            1,
            'Only one InclusiveNamespaces element supported per Transform.'
        );

        return new self($alg, array_pop($xpath), array_pop($prefixes));
    }


    /**
     * Convert this Transform element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Transform element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $e->setAttribute('Algorithm', $this->algorithm);

        switch ($this->algorithm) {
            case C::XPATH_URI:
                if ($this->xpath !== null) {
                    $this->xpath->toXML($e);
                }
                break;
            case C::C14N_EXCLUSIVE_WITH_COMMENTS:
            case C::C14N_EXCLUSIVE_WITHOUT_COMMENTS:
                if ($this->inclusiveNamespaces !== null) {
                    $this->inclusiveNamespaces->toXML($e);
                }
        }

        return $e;
    }
}
