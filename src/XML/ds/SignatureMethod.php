<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, SchemaViolationException, TooManyElementsException};
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\XML\Enumeration\NamespaceEnum;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

use function array_keys;
use function array_merge;
use function array_pop;
use function strval;

/**
 * Class representing a ds:SignatureMethod element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignatureMethod extends AbstractDsElement implements SchemaValidatableElementInterface
{
    use ExtendableElementTrait;
    use SchemaValidatableElementTrait;

    /** The namespace-attribute for the xs:any element */
    public const XS_ANY_ELT_NAMESPACE = NamespaceEnum::Other;


    /**
     * Initialize a SignatureMethod element.
     *
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue $Algorithm
     * @param \SimpleSAML\XMLSecurity\XML\ds\HMACOutputLength|null $hmacOutputLength
     * @param array<\SimpleSAML\XML\SerializableElementInterface> $children
     */
    public function __construct(
        protected AnyURIValue $Algorithm,
        protected ?HMACOutputLength $hmacOutputLength = null,
        array $children = [],
    ) {
        Assert::oneOf(
            $Algorithm->getValue(),
            array_merge(
                array_keys(C::$RSA_DIGESTS),
                array_keys(C::$HMAC_DIGESTS),
            ),
            'Invalid signature method: %s',
            InvalidArgumentException::class,
        );

        $this->setElements($children);
    }


    /**
     * Collect the value of the Algorithm-property
     *
     * @return \SimpleSAML\XMLSchema\Type\AnyURIValue
     */
    public function getAlgorithm(): AnyURIValue
    {
        return $this->Algorithm;
    }


    /**
     * Collect the value of the hmacOutputLength-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\HMACOutputLength|null
     */
    public function getHMACOutputLength(): ?HMACOutputLength
    {
        return $this->hmacOutputLength;
    }


    /**
     * Convert XML into a SignatureMethod
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SignatureMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignatureMethod::NS, InvalidDOMElementException::class);

        $Algorithm = self::getAttribute($xml, 'Algorithm', AnyURIValue::class);

        $hmacOutputLength = HMACOutputLength::getChildrenOfClass($xml);
        Assert::maxCount($hmacOutputLength, 1, TooManyElementsException::class);

        return new static($Algorithm, array_pop($hmacOutputLength), self::getChildElementsFromXML($xml));
    }


    /**
     * Convert this SignatureMethod element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignatureMethod element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', strval($this->getAlgorithm()));

        $this->getHMACOutputLength()?->toXML($e);

        foreach ($this->getElements() as $elt) {
            $elt->toXML($e);
        }

        return $e;
    }
}
