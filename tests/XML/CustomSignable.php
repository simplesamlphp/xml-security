<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\SignableElementInterface;
use SimpleSAML\XMLSecurity\XML\SignableElementTrait;

/**
 * @package simplesamlphp/xml-security
 */
class CustomSignable extends AbstractXMLElement implements SignableElementInterface
{
    use SignableElementTrait;

    /** @var string */
    public const NS = 'urn:ssp:custom';

    /** @var string */
    public const NS_PREFIX = 'ssp';

    /** @var \DOMElement $element */
    protected \DOMElement $element;

    /** @var bool */
    protected bool $formatOutput = false;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\Signature|null */
    protected ?Signature $signature = null;


    /**
     * Constructor
     *
     * @param \DOMElement $elt
     */
    public function __construct(DOMElement $elt) {
        $this->setElement($elt);
    }


    /**
     * Get the namespace for the element.
     *
     * @return string
     */
    public static function getNamespaceURI(): string
    {
        return static::NS;
    }


    /**
     * Get the namespace-prefix for the element.
     *
     * @return string
     */
    public static function getNamespacePrefix(): string
    {
        return static::NS_PREFIX;
    }


    /**
     * Collect the value of the $element property
     *
     * @return \DOMElement
     */
    public function getElement(): DOMElement
    {
        return $this->element;
    }


    /**
     * Set the value of the elment-property
     *
     * @param \DOMElement $elt
     */
    private function setElement(DOMElement $elt): void
    {
        $this->element = $elt;
    }


    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return null;
    }


    /**
     * Convert XML into a CustomSignable
     *
     * @param \DOMElement $xml The XML element we should load
     * @return \SimpleSAML\XMLSecurity\Test\XML\CustomSignable
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'CustomSignable', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        Assert::minCount($xml->childNodes, 1, MissingElementException::class);
        Assert::maxCount($xml->childNodes, 2, TooManyElementsException::class);

        $signature = Signature::getChildrenOfClass($xml);
        Assert::maxCount($signature, 1, TooManyElementsException::class);

        $customSignable = new self($xml->childNodes[(empty($signature) ? 0 : 1)]);
        if (!empty($signature)) {
            $customSignable->signature = $signature[0];
        }
        return $customSignable;
    }


    /**
     * Convert this CustomSignable to XML.
     *
     * @param \DOMElement|null $parent The parent element to append this CustomSignable to.
     * @return \DOMElement The XML element after adding the data corresponding to this CustomSignable.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        /** @psalm-var \DOMDocument $e->ownerDocument */
        $e = $this->instantiateParentElement($parent);

        $node = $e->appendChild($e->ownerDocument->importNode($this->element, true));

        if ($this->signer !== null) {
            $this->doSign($e);
        }

        if ($this->signature !== null) {
            $this->insertBefore($e, $node, $this->signature->toXML($e));
        }

        return $e;
    }
}
