<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;

use function array_pop;
use function sprintf;

/**
 * A class implementing the xenc:AbstractEncryptionMethod element.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractEncryptionMethod extends AbstractXencElement
{
    /** @var string */
    protected string $algorithm;

    /** @var \SimpleSAML\XMLSecurity\XML\xenc\KeySize|null */
    protected ?KeySize $keySize = null;

    /** @var \SimpleSAML\XMLSecurity\XML\xenc\OAEPparams|null */
    protected ?OAEPparams $oaepParams = null;

    /** @var \SimpleSAML\XML\Chunk[] */
    protected array $children = [];


    /**
     * EncryptionMethod constructor.
     *
     * @param string $algorithm
     * @param \SimpleSAML\XMLSecurity\XML\xenc\KeySize|null $keySize
     * @param \SimpleSAML\XMLSecurity\XML\xenc\OAEPparams|null $oaepParams
     * @param \SimpleSAML\XML\Chunk[] $children
     */
    final public function __construct(
        string $algorithm,
        ?KeySize $keySize = null,
        ?OAEPparams $oaepParams = null,
        array $children = [],
    ) {
        $this->setAlgorithm($algorithm);
        $this->setKeySize($keySize);
        $this->setOAEPParams($oaepParams);
        $this->setChildren($children);
    }


    /**
     * Get the URI identifying the algorithm used by this encryption method.
     *
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }


    /**
     * Set the URI identifying the algorithm used by this encryption method.
     *
     * @param string $algorithm
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    protected function setAlgorithm(string $algorithm): void
    {
        Assert::validURI($algorithm, SchemaViolationException::class); // Covers the empty string
        $this->algorithm = $algorithm;
    }


    /**
     * Get the size of the key used by this encryption method.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\KeySize|null
     */
    public function getKeySize(): ?KeySize
    {
        return $this->keySize;
    }


    /**
     * Set the size of the key used by this encryption method.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\KeySize|null $keySize
     */
    protected function setKeySize(?KeySize $keySize): void
    {
        $this->keySize = $keySize;
    }


    /**
     * Get the OAEP parameters.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\OAEPparams|null
     */
    public function getOAEPParams(): ?OAEPparams
    {
        return $this->oaepParams;
    }


    /**
     * Set the OAEP parameters.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\OAEPparams|null $oaepParams The OAEP parameters.
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    protected function setOAEPParams(?OAEPparams $oaepParams): void
    {
        $this->oaepParams = $oaepParams;
    }


    /**
     * Get the children elements of this encryption method as chunks.
     *
     * @return \SimpleSAML\XML\Chunk[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }


    /**
     * Set an array of chunks as children of this encryption method.
     *
     * @param \SimpleSAML\XML\Chunk[] $children
     * @throws \SimpleSAML\Assert\AssertionFailedException
     */
    protected function setChildren(array $children): void
    {
        Assert::allIsInstanceOf(
            $children,
            Chunk::class,
            sprintf(
                'All children elements of %s:EncryptionMethod must be of type \SimpleSAML\XML\Chunk.',
                static::NS_PREFIX
            ),
            InvalidArgumentException::class,
        );

        $this->children = $children;
    }


    /**
     * Initialize an EncryptionMethod object from an existing XML.
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
        Assert::same($xml->localName, 'EncryptionMethod', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        /** @psalm-var string $algorithm */
        $algorithm = self::getAttribute($xml, 'Algorithm');

        $keySize = KeySize::getChildrenOfClass($xml);
        Assert::maxCount($keySize, 1, TooManyElementsException::class);

        $oaepParams = OAEPparams::getChildrenOfClass($xml);
        Assert::maxCount($oaepParams, 1, TooManyElementsException::class);

        $children = [];
        foreach ($xml->childNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            } elseif ($node->namespaceURI === C::NS_XENC) {
                if ($node->localName === 'KeySize') {
                    continue;
                } elseif ($node->localName === 'OAEPparams') {
                    continue;
                }
            }

            $children[] = Chunk::fromXML($node);
        }

        return new static($algorithm, array_pop($keySize), array_pop($oaepParams), $children);
    }


    /**
     * Convert this EncryptionMethod object to XML.
     *
     * @param \DOMElement|null $parent The element we should append this EncryptionMethod to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        /** @psalm-var \DOMDocument $e->ownerDocument */
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Algorithm', $this->getAlgorithm());

        $this->getKeySize()?->toXML($e);
        $this->getOAEPparams()?->toXML($e);

        foreach ($this->getChildren() as $child) {
            $child->toXML($e);
        }

        return $e;
    }
}
