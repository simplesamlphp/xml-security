<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;

/**
 * Class representing a ds:KeyInfo element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfo extends AbstractDsElement
{
    /**
     * Initialize a KeyInfo element.
     *
     * @param (
     *     \SimpleSAML\XML\SerializableElementInterface|
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyName|
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyValue|
     *     \SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod|
     *     \SimpleSAML\XMLSecurity\XML\ds\X509Data|
     *     \SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference|
     *     \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData|
     *     \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey
     * )[] $info
     * @param string|null $Id
     */
    public function __construct(
        protected array $info,
        protected ?string $Id = null,
    ) {
        Assert::notEmpty($info, 'ds:KeyInfo cannot be empty', InvalidArgumentException::class);
        Assert::maxCount($info, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOfAny(
            $info,
            [
                Chunk::class,
                KeyName::class,
                KeyValue::class,
                RetrievalMethod::class,
                X509Data::class,
                EncryptedData::class,
                EncryptedKey::class,
            ],
            'KeyInfo can only contain instances of KeyName, X509Data, EncryptedKey or Chunk.',
            InvalidArgumentException::class,
        );
        Assert::nullOrValidNCName($Id);
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
     * Collect the value of the info-property
     *
     * @return (
     *     \SimpleSAML\XML\SerializableElementInterface|
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyName|
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyValue|
     *     \SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod|
     *     \SimpleSAML\XMLSecurity\XML\ds\X509Data|
     *     \SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference|
     *     \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData|
     *     \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey
     * )[]
     */
    public function getInfo(): array
    {
        return $this->info;
    }


    /**
     * Convert XML into a KeyInfo
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'KeyInfo', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, KeyInfo::NS, InvalidDOMElementException::class);

        $Id = self::getOptionalAttribute($xml, 'Id', null);
        $info = [];

        foreach ($xml->childNodes as $n) {
            if (!($n instanceof DOMElement)) {
                continue;
            } elseif ($n->namespaceURI === C::NS_XDSIG) {
                $info[] = match ($n->localName) {
                    'KeyName' => KeyName::fromXML($n),
                    'KeyValue' => KeyValue::fromXML($n),
                    'RetrievalMethod' => RetrievalMethod::fromXML($n),
                    'X509Data' => X509Data::fromXML($n),
                    default => new Chunk($n),
                };
            } elseif ($n->namespaceURI === C::NS_XDSIG11) {
                $info[] = match ($n->localName) {
                    'KeyInfoReference' => KeyInfoReference::fromXML($n),
                    default => new Chunk($n),
                };
            } elseif ($n->namespaceURI === C::NS_XENC) {
                $info[] = match ($n->localName) {
                    'EncryptedData' => EncryptedData::fromXML($n),
                    'EncryptedKey' => EncryptedKey::fromXML($n),
                    default => new Chunk($n),
                };
            } else {
                $info[] = new Chunk($n);
            }
        }

        return new static($info, $Id);
    }


    /**
     * Convert this KeyInfo to XML.
     *
     * @param \DOMElement|null $parent The element we should append this KeyInfo to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getId() !== null) {
            $e->setAttribute('Id', $this->getId());
        }

        foreach ($this->getInfo() as $n) {
            $n->toXML($e);
        }

        return $e;
    }
}
