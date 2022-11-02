<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XML\Base64ElementTrait;

/**
 * Class representing a dsig11:X509Digest element.
 *
 * @package simplesaml/xml-security
 * @psalm-suppress PropertyNotSetInConstructor $content
 */
final class X509Digest extends AbstractDsig11Element
{
    use Base64ElementTrait;

    /**
     * The digest algorithm.
     *
     * @var string
     */
    protected string $algorithm;


    /**
     * Initialize a X509Digest element.
     *
     * @param string $digest
     * @param string $algorithm
     */
    public function __construct(string $digest, string $algorithm)
    {
        $this->setContent($digest);
        $this->setAlgorithm($algorithm);
    }


    /**
     * Collect the value of the algorithm-property
     *
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }


    /**
     * Set the value of the algorithm-property
     *
     * @param string $algorithm
     */
    private function setAlgorithm(string $algorithm): void
    {
        Assert::validURI($algorithm, SchemaViolationException::class);
        Assert::oneOf(
            $algorithm,
            array_keys(C::$DIGEST_ALGORITHMS),
            'Invalid digest method: %s',
            InvalidArgumentException::class,
        );

        $this->algorithm = $algorithm;
    }


    /**
     * Convert XML into a X509Digest
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'X509Digest', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, X509Digest::NS, InvalidDOMElementException::class);

        /** @psalm-var string $algorithm */
        $algorithm = self::getAttribute($xml, 'Algorithm');

        return new static($xml->textContent, $algorithm);
    }


    /**
     * Convert this X509Digest element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this X509Digest element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = $this->getContent();
        $e->setAttribute('Algorithm', $this->getAlgorithm());

        return $e;
    }
}
