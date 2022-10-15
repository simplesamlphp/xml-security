<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\Constants as C;

use function array_pop;

/**
 * Class representing a ds:Signature element.
 *
 * @package simplesamlphp/xml-security
 */
final class Signature extends AbstractDsElement
{
    /** @var \SimpleSAML\XMLSecurity\XML\ds\SignedInfo */
    protected SignedInfo $signedInfo;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\SignatureValue */
    protected SignatureValue $signatureValue;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo */
    protected ?KeyInfo $keyInfo;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\DsObject[] */
    protected array $objects;

    /** @var string|null */
    protected ?string $Id;


    /**
     * Signature constructor.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignedInfo $signedInfo
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureValue $signatureValue
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo
     * @param \SimpleSAML\XMLSecurity\XML\ds\DsObject[] $objects
     * @param string|null $Id
     */
    public function __construct(
        SignedInfo $signedInfo,
        SignatureValue $signatureValue,
        ?KeyInfo $keyInfo,
        array $objects = [],
        ?string $Id = null,
    ) {
        $this->setSignedInfo($signedInfo);
        $this->setSignatureValue($signatureValue);
        $this->setKeyInfo($keyInfo);
        $this->setObjects($objects);
        $this->setId($Id);
    }


    /**
     * Get the Id used for this signature.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->Id;
    }


    /**
     * Set the Id used for this signature.
     *
     * @param string|null $Id
     */
    protected function setId(?string $Id): void
    {
        Assert::nullOrValidNCName($Id);
        $this->Id = $Id;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignedInfo $signedInfo
     */
    protected function setSignedInfo(SignedInfo $signedInfo): void
    {
        $this->signedInfo = $signedInfo;
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\SignedInfo
     */
    public function getSignedInfo(): SignedInfo
    {
        return $this->signedInfo;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureValue $signatureValue
     */
    protected function setSignatureValue(SignatureValue $signatureValue): void
    {
        $this->signatureValue = $signatureValue;
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\SignatureValue
     */
    public function getSignatureValue(): SignatureValue
    {
        return $this->signatureValue;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo
     */
    protected function setKeyInfo(?KeyInfo $keyInfo): void
    {
        $this->keyInfo = $keyInfo;
    }


    /**
     * @return \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null
     */
    public function getKeyInfo(): ?KeyInfo
    {
        return $this->keyInfo;
    }


    /**
     * Get the array of ds:Object elements attached to this signature.
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\DsObject[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }


    /**
     * Set the array of ds:Object elements attached to this signature.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\DsObject[] $objects
     */
    protected function setObjects(array $objects): void
    {
        Assert::allIsInstanceOf($objects, DsObject::class);
        $this->objects = $objects;
    }


    /**
     * Convert XML into a Signature element
     *
     * @param \DOMElement $xml
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Signature', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Signature::NS, InvalidDOMElementException::class);

        $Id = self::getAttribute($xml, 'Id', null);

        $signedInfo = SignedInfo::getChildrenOfClass($xml);
        Assert::minCount(
            $signedInfo,
            1,
            'ds:Signature needs exactly one ds:SignedInfo element.',
            MissingElementException::class,
        );
        Assert::maxCount(
            $signedInfo,
            1,
            'ds:Signature needs exactly one ds:SignedInfo element.',
            TooManyElementsException::class,
        );

        $signatureValue = SignatureValue::getChildrenOfClass($xml);
        Assert::minCount(
            $signatureValue,
            1,
            'ds:Signature needs exactly one ds:SignatureValue element.',
            MissingElementException::class,
        );
        Assert::maxCount(
            $signatureValue,
            1,
            'ds:Signature needs exactly one ds:SignatureValue element.',
            TooManyElementsException::class,
        );

        $keyInfo = KeyInfo::getChildrenOfClass($xml);
        Assert::maxCount(
            $keyInfo,
            1,
            'ds:Signature can hold a maximum of one ds:KeyInfo element.',
            TooManyElementsException::class,
        );

        $objects = DsObject::getChildrenOfClass($xml);

        return new static(
            array_pop($signedInfo),
            array_pop($signatureValue),
            empty($keyInfo) ? null : array_pop($keyInfo),
            $objects,
            $Id,
        );
    }


    /**
     * Convert this Signature element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this Signature element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getId() !== null) {
            $e->setAttribute('Id', $this->getId());
        }

        $this->getSignedInfo()->toXML($e);
        $this->getSignatureValue()->toXML($e);

        if ($this->getKeyInfo() !== null) {
            $this->getKeyInfo()->toXML($e);
        }

        foreach ($this->getObjects() as $o) {
            $o->toXML($e);
        }

        return $e;
    }
}
