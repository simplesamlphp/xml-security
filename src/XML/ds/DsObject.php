<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:Object element.
 *
 * @package simplesamlphp/xml-security
 */
final class DsObject extends AbstractDsElement
{
    use ExtendableElementTrait;

    /** @var string */
    public const LOCALNAME = 'Object';

    /**
     * The Id.
     *
     * @var string|null
     */
    protected ?string $Id;

    /**
     * The MIME type.
     *
     * @var string|null
     */
    protected ?string $MimeType;

    /**
     * The encoding.
     *
     * @var string|null
     */
    protected ?string $Encoding;


    /**
     * Initialize a ds:Object element.
     *
     * @param string|null $Id
     * @param string|null $MimeType
     * @param string|null $Encoding
     * @param \SimpleSAML\XML\XMLElementInterface[] $elements
     */
    public function __construct(
        ?string $Id = null,
        ?string $MimeType = null,
        ?string $Encoding = null,
        array $elements = []
    ) {
        $this->setId($Id);
        $this->setMimeType($MimeType);
        $this->setEncoding($Encoding);
        $this->setElements($elements);
    }


    /**
     * Collect the value of the Id-property
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->Id;
    }


    /**
     * Set the value of the Id-property
     *
     * @param string $Id
     */
    private function setId(?string $Id): void
    {
        $this->Id = $Id;
    }


    /**
     * Collect the value of the MimeType-property
     *
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->MimeType;
    }


    /**
     * Set the value of the MimeType-property
     *
     * @param string|null $MimeType
     */
    private function setMimeType(?string $MimeType): void
    {
        $this->MimeType = $MimeType;
    }


    /**
     * Collect the value of the Encoding-property
     *
     * @return string|null
     */
    public function getEncoding(): ?string
    {
        return $this->Encoding;
    }


    /**
     * Set the value of the Encoding-property
     *
     * @param string|null $Encoding
     */
    private function setEncoding(?string $Encoding): void
    {
        $this->Encoding = $Encoding;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     *
     * @return bool
     */
    public function isEmptyElement(): bool
    {
        return (
            empty($this->elements)
            && empty($this->Id)
            && empty($this->MimeType)
            && empty($this->Encoding)
        );
    }


    /**
     * Convert XML into a ds:Object
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'Object', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Object::NS, InvalidDOMElementException::class);

        $Id = Object::getAttribute($xml, 'Id');
        $MimeType = Object::getAttribute($xml, 'MimeType');
        $Encoding = Object::getAttribute($xml, 'Encoding');

        $elements = [];
        foreach ($xml->childNodes as $elt) {
            if (!($elt instanceof DOMElement)) {
                continue;
            }

            $elements[] = new Chunk($elt);
        }

        return new self($Id, $MimeType, $Encoding, $elements);
    }


    /**
     * Convert this ds:Object element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this ds:Object element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Id', $this->Id);
        $e->setAttribute('MimeType', $this->MimeType);
        $e->setAttribute('Encoding', $this->Encoding);

        foreach ($this->elements as $elt) {
            $elt->toXML($e);
        }

        return $e;
    }
}
