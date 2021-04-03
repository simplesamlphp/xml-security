<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:Reference element.
 *
 * @package simplesamlphp/xml-security
 */
final class Reference extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\Transforms|null */
    protected Transforms $transforms;

    /** @var \SimpleSAML\XMLSecurity\DigestMethod */
    protected DigestMethod $digestMethod;

    /** @var \SimpleSAML\XMLSecurity\DigestValue */
    protected DigestValue $digestValue;

    /** @var string|null $Id */
    protected ?string $Id;

    /** @var string|null $type */
    protected ?string $Type;

    /** @var string|null $URI */
    protected ?string $URI;


    /**
     * Initialize a ds:Reference
     *
     * @param \SimpleSAML\XMLSecurity\DigestMethod $digestMethod
     * @param \SimpleSAML\XMLSecurity\DigestValue $digestValue
     * @param \SimpleSAML\XMLSecurity\Transforms|null $transforms
     * @param string|null $Id
     * @param string|null $Type
     * @param string|null $URI
     */
    public function __construct(
        DigestMethod $digestMethod,
        DigestValue $digestValue,
        ?Transforms $transforms = null,
        ?string $Id = null,
        ?string $Type = null,
        ?string $URI = null
    ) {
        $this->setTransforms($transforms);
        $this->setDigestMethod($digestMethod);
        $this->setDigestValue($digestValue);
        $this->setId($ID);
        $this->setType($Type);
        $this->setURI($URI);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\Transforms|null
     */
    public function getTransforms(): ?Transforms
    {
        return $this->transforms;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\Transforms|null
     */
    protected function setTransforms(?Transforms $transforms): void
    {
        $this->transforms = $transforms;
    }


    /**
     * @return \SimpleSAML\XMLSecurity\DigestMethod
     */
    public function getDigestMethod(): DigestMethod
    {
        return $this->digestMethod;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\DigestMethod $digestMethod
     */
    private function setDigestMethod(DigestMethod $digestMethod): void
    {
        $this->digestMethod = $digestMethod;
    }


    /**
     * @return \SimpleSAML\XMLSecurity\DigestValue
     */
    public function getDigestValue(): DigestValue
    {
        return $this->digestValue;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\DigestValue $digestValue
     */
    private function setDigestValue(DigestValue $digestValue): void
    {
        $this->digestValue = $digestValue;
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->Id;
    }


    /**
     * @param string|null $Id
     */
    private function setId(?string $Id): void
    {
        $this->Id = $Id;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->Type;
    }


    /**
     * @param string|null $Type
     */
    private function setType(?string $Type): void
    {
        $this->Type = $Type;
    }


    /**
     * @return string
     */
    public function getURI(): string
    {
        return $this->URI;
    }


    /**
     * @param string|null $URI
     */
    private function setURI(?string $URI): void
    {
        $this->URI = $URI;
    }


   /**
     * Convert XML into a Reference element
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'Reference', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Reference::NS, InvalidDOMElementException::class);

        $Id = self::getAttribute($xml, 'Id', null);
        $Type = self::getAttribute($xml, 'Type', null);
        $URI = self::getAttribute($xml, 'URI', null);

        $transforms = Transforms::getChildrenOfClass($xml);
        Assert::maxCount(1, $transforms, 'A <ds:Reference> may contain just on <ds:Transforms>.');

        $digestMethod = DigestMethod::getChildrenOfClass($xml);
        Assert::count(1, $digestMethod, 'A <ds:Reference> must contain a <ds:DigestMethod>.');

        $digestValue = DigestValue::getChildrenOfClass($xml);
        Assert::count(1, $digestValue, 'A <ds:Reference> must contain a <ds:DigestValue>.');

        return new self(
            array_pop($digestMethod),
            array_pop($digestValue),
            empty($transforms) ? null : array_pop($transforms),
            $Id,
            $Type,
            $URI
        );
    }


    /**
     * Convert this Reference element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Reference element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Id', $this->Id);
        $e->setAttribute('Type', $this->Type);
        $e->setAttribute('URI', $this->URI);

        if ($this->transforms !== null) {
            $this->transforms->toXML($e);
        }

        $this->digestMethod->toXML($e);
        $this->digestValue->toXML($e);

        return $e;
    }
}