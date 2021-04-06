<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Constants;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Class representing a ds:X509SerialNumber element.
 *
 * @package simplesaml/xml-security
 */
final class X509SerialNumber extends AbstractDsElement
{
    /**
     * The serial number.
     *
     * @var int
     */
    protected int $serial;


    /**
     * Initialize a X509SerialNumber element.
     *
     * @param int $serial
     */
    public function __construct(int $serial)
    {
        $this->setSerial($serial);
    }


    /**
     * Collect the value of the serial-property
     *
     * @return int
     */
    public function getSerial(): int
    {
        return $this->serial;
    }


    /**
     * Set the value of the serial-property
     *
     * @param int $serial
     */
    private function setSerial(int $serial): void
    {
        $this->serial = $serial;
    }


    /**
     * Convert XML into a X509SerialNumber
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'X509SerialNumber', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, X509SerialNumber::NS, InvalidDOMElementException::class);
        Assert::same($xml->getAttributeNS(Constants::NS_XSI, "type"), 'xs:integer');

        return new self(intval($xml->textContent));
    }


    /**
     * Convert this X509SerialNumber element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this X509SerialNumber element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $e->setAttributeNS(Constants::NS_XSI, 'xsi:type', 'xs:integer');
        $e->textContent = strval($this->serial);

        return $e;
    }
}
