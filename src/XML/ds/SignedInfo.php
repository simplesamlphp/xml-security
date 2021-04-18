<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Chunk;

/**
 * Class representing a ds:SignedInfo element.
 *
 * @package simplesamlphp/xml-security
 */
final class SignedInfo extends AbstractDsElement
{
    /** @var string */
    protected string $Id;

    /**
     * @var \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod
     */
    protected CanonicalizationMethod $canonicalizationMethod;

    /**
     * @var \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod
     */
    protected SignatureMethod $signatureMethod;

    /**
     * @var \SimpleSAML\XMLSecurity\XML\ds\Reference[]
     */
    protected array $references;


    /**
     * Initialize a SignedIfno.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod $canonicalizationMethod
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod $signatureMethod
     * @param \SimpleSAML\XMLSecurity\XML\ds\Reference[] $references
     * @param string|null $Id
     */
    public function __construct(
        CanonicalizationMethod $canonicalizationMethod,
        SignatureMethod $signatureMethod,
        array $references,
        ?string $Id = null
    ) {
        $this->setCanonicalizationMethod($canonicalizationMethod);
        $this->setSignatureMethod($signatureMethod);
        $this->setReferences($references);
        $this->setId($Id);
    }


    /**
     * Collect the value of the canonicalizationMethod-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod
     */
    public function getCanonicalizationMethod(): CanonicalizationMethod
    {
        return $this->canonicalizationMethod;
    }


    /**
     * Set the value of the canonicalizationMethod-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod $canonicalizationMethod
     */
    private function setCanonicalizationMethod(CanonicalizationMethod $canonicalizationMethod): void
    {
        $this->canonicalizationMethod = $canonicalizationMethod;
    }


    /**
     * Collect the value of the signatureMethod-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod
     */
    public function getSignatureMethod(): SignatureMethod
    {
        return $this->signatureMethod;
    }


    /**
     * Set the value of the signatureMethod-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\SignatureMethod $signatureMethod
     */
    private function setSignatureMethod(SignatureMethod $signatureMethod): void
    {
        $this->signatureMethod = $signatureMethod;
    }


    /**
     * Collect the value of the references-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\Reference[]
     */
    public function getReferences(): array
    {
        return $this->references;
    }


    /**
     * Set the value of the references-property
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Reference[] $references
     */
    private function setReferences(array $references): void
    {
        Assert::allIsInstanceOf($references, Reference::class);

        $this->references = $references;
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
     * @param string|null $Id
     */
    private function setId(?string $Id): void
    {
        $this->Id = $Id;
    }


    /**
     * Convert XML into a SignedInfo instance
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'SignedInfo', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, SignedInfo::NS, InvalidDOMElementException::class);

        $Id = self::getAttribute($xml, 'Id', null);

        $canonicalizationMethod = CanonicalizationMethod::getChildrenOfClass($xml);
        Assert::count($canonicalizationMethod, 1, 'A ds:SignedInfo element must contain exactly one ds:CanonicalizationMethod');

        $signatureMethod = SignatureMethod::getChildrenOfClass($xml);
        Assert::count($signatureMethod, 1, 'A ds:SignedInfo element must contain exactly one ds:SignatureMethod');

        $references = Reference::getChildrenOfClass($xml);
        Assert::minCount($references, 1, 'A ds:SignedInfo element must contain at least one ds:Reference');

        return new self(
            array_pop($canonicalizationMethod),
            array_pop($signatureMethod),
            $references,
            $Id
        );
    }


    /**
     * Convert this SignedInfo element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this SignedInfo element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->Id !== null) {
            $e->setAttribute('Id', $this->Id);
        }

        $this->canonicalizationMethod->toXML($e);
        $this->signatureMethod->toXML($e);

        foreach ($this->references as $ref) {
            $ref->toXML($e);
        }

        return $e;
    }
}
