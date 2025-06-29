<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\{InvalidDOMElementException, SchemaViolationException, TooManyElementsException};
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\AnyURIValue;
use SimpleSAML\XML\XsNamespace as NS;

use function array_pop;
use function strval;

/**
 * A class implementing the xenc:AbstractAgreementMethodType element.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractAgreementMethodType extends AbstractXencElement implements SchemaValidatableElementInterface
{
    use ExtendableElementTrait;
    use SchemaValidatableElementTrait;

    /** The namespace-attribute for the xs:any element */
    public const XS_ANY_ELT_NAMESPACE = NS::OTHER;


    /**
     * AgreementMethodType constructor.
     *
     * @param \SimpleSAML\XML\Type\AnyURIValue $algorithm
     * @param \SimpleSAML\XMLSecurity\XML\xenc\KANonce|null $kaNonce
     * @param \SimpleSAML\XMLSecurity\XML\xenc\OriginatorKeyInfo|null $originatorKeyInfo
     * @param \SimpleSAML\XMLSecurity\XML\xenc\RecipientKeyInfo|null $recipientKeyInfo
     * @param list<\SimpleSAML\XML\SerializableElementInterface> $children
     */
    final public function __construct(
        protected AnyURIValue $algorithm,
        protected ?KANonce $kaNonce = null,
        protected ?OriginatorKeyInfo $originatorKeyInfo = null,
        protected ?RecipientKeyInfo $recipientKeyInfo = null,
        protected array $children = [],
    ) {
        $this->setElements($children);
    }


    /**
     * Get the URI identifying the algorithm used by this agreement method.
     *
     * @return \SimpleSAML\XML\Type\AnyURIValue
     */
    public function getAlgorithm(): AnyURIValue
    {
        return $this->algorithm;
    }


    /**
     * Get the KA-Nonce.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\KANonce|null
     */
    public function getKANonce(): ?KANonce
    {
        return $this->kaNonce;
    }


    /**
     * Get the Originator KeyInfo.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\OriginatorKeyInfo|null
     */
    public function getOriginatorKeyInfo(): ?OriginatorKeyInfo
    {
        return $this->originatorKeyInfo;
    }


    /**
     * Get the Recipient KeyInfo.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\RecipientKeyInfo|null
     */
    public function getRecipientKeyInfo(): ?RecipientKeyInfo
    {
        return $this->recipientKeyInfo;
    }


    /**
     * Initialize an AgreementMethod object from an existing XML.
     *
     * @param \DOMElement $xml
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XML\Exception\MissingAttributeException
     *   if the supplied element is missing one of the mandatory attributes
     * @throws \SimpleSAML\XML\Exception\TooManyElementsException
     *   if too many child-elements of a type are specified
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'AgreementMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $kaNonce = KANonce::getChildrenOfClass($xml);
        Assert::maxCount($kaNonce, 1, TooManyElementsException::class);

        $originatorKeyInfo = OriginatorKeyInfo::getChildrenOfClass($xml);
        Assert::maxCount($originatorKeyInfo, 1, TooManyElementsException::class);

        $recipientKeyInfo = RecipientKeyInfo::getChildrenOfClass($xml);
        Assert::maxCount($recipientKeyInfo, 1, TooManyElementsException::class);

        $children = self::getChildElementsFromXML($xml);

        return new static(
            self::getAttribute($xml, 'Algorithm', AnyURIValue::class),
            array_pop($kaNonce),
            array_pop($originatorKeyInfo),
            array_pop($recipientKeyInfo),
            $children,
        );
    }


    /**
     * Convert this AgreementMethod object to XML.
     *
     * @param \DOMElement|null $parent The element we should append this AgreementMethod to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', strval($this->getAlgorithm()));

        $this->getKANonce()?->toXML($e);

        foreach ($this->getElements() as $child) {
            $child->toXML($e);
        }

        $this->getOriginatorKeyInfo()?->toXML($e);
        $this->getRecipientKeyInfo()?->toXML($e);

        return $e;
    }
}
