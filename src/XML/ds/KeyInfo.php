<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\SerializableElementInterface;
use SimpleSAML\XML\XsNamespace as NS;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

/**
 * Class representing a ds:KeyInfo element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyInfo extends AbstractDsElement
{
    use ExtendableElementTrait;

    /** @var \SimpleSAML\XML\XsNamespace */
    public const XS_ANY_ELT_NAMESPACE = NS::OTHER;


    /**
     * Initialize a KeyInfo element.
     *
     * @param (
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyName|
     *     \SimpleSAML\XMLSecurity\XML\ds\KeyValue|
     *     \SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod|
     *     \SimpleSAML\XMLSecurity\XML\ds\X509Data|
     *     \SimpleSAML\XML\SerializableElementInterface
     * )[] $info
     * @param string|null $Id
     */
    public function __construct(
        protected array $info,
        protected ?string $Id = null,
    ) {
        Assert::notEmpty($info, 'ds:KeyInfo cannot be empty', InvalidArgumentException::class);
        Assert::maxCount($info, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf(
            $info,
            SerializableElementInterface::class,
            InvalidArgumentException::class,
        );
        Assert::nullOrValidNCName($Id);

        foreach ($info as $item) {
            if ($item->getNamespaceURI() === static::NS) {
                Assert::isInstanceOfAny(
                    $item,
                    [KeyName::class, KeyValue::class, RetrievalMethod::class, X509Data::class],
                    SchemaViolationException::class,
                );
            }
        }
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
     * @return list<\SimpleSAML\XML\SerializableElementInterface>
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

        $keyName = KeyName::getChildrenOfClass($xml);
        $keyValue = KeyValue::getChildrenOfClass($xml);
        $retrievalMethod = RetrievalMethod::getChildrenOfClass($xml);
        $x509Data = X509Data::getChildrenOfClass($xml);
        //$pgpData = PGPData::getChildrenOfClass($xml);
        //$spkiData = SPKIData::getChildrenOfClass($xml);
        //$mgmtData = MgmtData::getChildrenOfClass($xml);
        $other = self::getChildElementsFromXML($xml);

        $info = array_merge(
            $keyName,
            $keyValue,
            $retrievalMethod,
            $x509Data,
            //$pgpdata,
            //$spkidata,
            //$mgmtdata,
            $other,
        );

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

        foreach ($this->getInfo() as $elt) {
            $elt->toXML($e);
        }

        return $e;
    }
}
