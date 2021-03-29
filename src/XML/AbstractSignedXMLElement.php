<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\XML\ds\Signature;

/**
 * Abstract class to be implemented by all signed classes
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractSignedXMLElement implements SignedElementInterface
{
    use SignedElementTrait;

    /**
     * The signed DOM structure.
     *
     * @var \DOMElement
     */
    protected DOMElement $structure;

    /**
     * The unsigned elelement.
     *
     * @var \SimpleSAML\XMLSecurity\XML\SignableElementInterface
     */
    protected SignableElementInterface $element;


    /**
     * Create/parse an alg:SigningMethod element.
     *
     * @param \DOMElement $xml
     * @param \SimpleSAML\XMLSecurity\XML\ds\Signature $signature
     */
    protected function __construct(DOMElement $xml, Signature $signature)
    {
        $this->setStructure($xml);
        $this->setSignature($signature);
    }


    /**
     * Output the class as an XML-formatted string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->structure->ownerDocument->saveXML();
    }


    /**
     * Collect the value of the structure-property
     *
     * @return \DOMElement
     */
    public function getStructure(): DOMElement
    {
        return $this->structure;
    }


    /**
     * Set the value of the structure-property
     *
     * @param \DOMElement $structure
     */
    private function setStructure(DOMElement $structure): void
    {
        $this->structure = $structure;
    }


    /**
     * Create XML from this class
     *
     * @param \DOMElement|null $parent
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        return $this->structure;
    }


    /**
     * Create a class from XML
     *
     * @param \DOMElement $xml
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        $signature = Signature::getChildrenOfClass($xml);
        Assert::minCount($signature, 1, MissingElementException::class);
        Assert::maxCount($signature, 1, TooManyElementsException::class);

        return new self($xml, array_pop($signature));
    }
}