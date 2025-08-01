<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, TooManyElementsException};
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSecurity\Backend\EncryptionBackend;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\{EncryptableElementInterface, EncryptableElementTrait};
use SimpleSAML\XMLSecurity\XML\{SignableElementInterface, SignableElementTrait};
use SimpleSAML\XMLSecurity\XML\{SignedElementInterface, SignedElementTrait};

/**
 * This is an example class demonstrating an object that can be signed and encrypted.
 *
 * @package simplesamlphp/xml-security
 */
class CustomSignable extends AbstractElement implements
    SignableElementInterface,
    SignedElementInterface,
    EncryptableElementInterface
{
    use SignableElementTrait;
    use SignedElementTrait;
    use EncryptableElementTrait;

    /** @var string */
    public const NS = 'urn:x-simplesamlphp:namespace';

    /** @var string */
    public const NS_PREFIX = 'ssp';

    /** @var bool */
    protected bool $formatOutput = false;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\Signature|null */
    protected ?Signature $signature = null;

    /** @var \SimpleSAML\XMLSecurity\Backend\EncryptionBackend|null */
    private ?EncryptionBackend $backend = null;

    /**
     * Constructor
     *
     * @param \DOMElement $xml
     * @param \SimpleSAML\XMLSchema\Type\IDValue $id
     */
    final public function __construct(
        protected DOMElement $xml,
        protected ?IDValue $id,
    ) {
    }


    /**
     * Get the namespace for the element.
     *
     * @return string
     */
    public static function getNamespaceURI(): string
    {
        return static::NS;
    }


    /**
     * Get the namespace-prefix for the element.
     *
     * @return string
     */
    public static function getNamespacePrefix(): string
    {
        return static::NS_PREFIX;
    }


    /**
     * Get the XML element.
     *
     * @return \DOMElement
     */
    public function getXML(): DOMElement
    {
        return $this->xml;
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->id;
    }


    /**
     * @inheritDoc
     */
    protected function getOriginalXML(): DOMElement
    {
        return $this->xml;
    }


    /**
     * Implement a method like this if your encrypted object needs to instantiate a new decryptor, for example, to
     * decrypt a session key. This method is required by \SimpleSAML\XMLSecurity\XML\EncryptedElementTrait.
     *
     * @return \SimpleSAML\XMLSecurity\Backend\EncryptionBackend|null The encryption backend to use, or null if we want
     * to use the default.
     */
    public function getEncryptionBackend(): ?EncryptionBackend
    {
        return $this->backend;
    }


    /**
     * Implement a method like this if your encrypted object needs to instantiate a new decryptor, for example, to
     * decrypt a session key. This method is required by \SimpleSAML\XMLSecurity\XML\EncryptedElementTrait.
     *
     * @param \SimpleSAML\XMLSecurity\Backend\EncryptionBackend|null $backend The encryption backend we want to use, or
     * null if we want to use the defaults.
     */
    public function setEncryptionBackend(?EncryptionBackend $backend): void
    {
        $this->backend = $backend;
    }


    /**
     * Implement a method like this if your encrypted object needs to instantiate a new decryptor, for example, to
     * decrypt a session key. This method is required by \SimpleSAML\XMLSecurity\XML\EncryptedElementTrait.
     *
     * @return string[]|null An array with all algorithm identifiers that are blacklisted, or null to use this
     * libraries default.
     */
    public function getBlacklistedAlgorithms(): ?array
    {
        return [];
    }


    /**
     * Convert XML into a CustomSignable
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'CustomSignable', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $id = self::getOptionalAttribute($xml, 'id', IDValue::class, null);
        $signature = Signature::getChildrenOfClass($xml);
        Assert::maxCount($signature, 1, TooManyElementsException::class);

        $customSignable = new static($xml, $id);
        if (!empty($signature)) {
            $customSignable->signature = $signature[0];
        }
        return $customSignable;
    }


    /**
     * Convert this CustomSignable to XML.
     *
     * @param \DOMElement|null $parent The parent element to append this CustomSignable to.
     * @return \DOMElement The XML element after adding the data corresponding to this CustomSignable.
     * @throws \Exception
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        if ($this->signer !== null) {
            $signedXML = $this->doSign($this->xml);
            $signedXML->insertBefore($this->signature->toXML($signedXML), $signedXML->firstChild);
            return $signedXML;
        }

        return $this->xml;
    }
}
