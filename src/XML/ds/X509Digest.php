<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class representing a ds:X509Digest element.
 *
 * @package simplesaml/xml-security
 */
final class X509Digest extends AbstractDsElement
{
    use XMLStringElementTrait {
        __construct as __parentConstruct;
        toXML as parentToXML;
    }


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
        $this->__parentConstruct($digest);

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
        Assert::oneOf(
            $algorithm,
            [
                Constants::DIGEST_SHA1,
                Constants::DIGEST_SHA224,
                Constants::DIGEST_SHA256,
                Constants::DIGEST_SHA384,
                Constants::DIGEST_SHA512,
                Constants::DIGEST_RIPEMD160,
            ],
            'Invalid digest method',
            InvalidArgumentException::class
        );

        $this->algorithm = $algorithm;
    }


    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    private function validateContent(string $content): void
    {
        Assert::notEmpty($content, 'X509Digest cannot be empty');
        Assert::stringPlausibleBase64($content, 'ds:X509Digest is not a valid Base64 encoded string');
    }


    /**
     * Convert XML into a X509Digest
     *
     * @param \DOMElement $xml The XML element we should load
     * @return self
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'X509Digest', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, X509Digest::NS, InvalidDOMElementException::class);

        $algorithm = self::getAttribute($xml, 'Algorithm');

        return new self($xml->textContent, $algorithm);
    }


    /**
     * Convert this X509Digest element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this X509Digest element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->parentToXML($parent);
        $e->setAttribute('Algorithm', $this->algorithm);

        return $e;
    }
}
