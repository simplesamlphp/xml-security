<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, SchemaViolationException};
use SimpleSAML\XMLSchema\Type\Builtin\IDValue;
use SimpleSAML\XMLSecurity\Assert\Assert;

use function strval;

/**
 * Class representing <xenc:EncryptionPropertiesType>.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractEncryptionPropertiesType extends AbstractXencElement implements
    SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * EncryptionProperty constructor.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty[] $encryptionProperty
     * @param \SimpleSAML\XMLSchema\Type\Builtin\IDValue|null $Id
     */
    final public function __construct(
        protected array $encryptionProperty,
        protected ?IDValue $Id = null,
    ) {
        Assert::minCount($encryptionProperty, 1, MissingElementException::class);
    }


    /**
     * Get the value of the $encryptionProperty property.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty[]
     */
    public function getEncryptionProperty(): array
    {
        return $this->encryptionProperty;
    }


    /**
     * Get the value of the $Id property.
     *
     * @return \SimpleSAML\XMLSchema\Type\Builtin\IDValue|null
     */
    public function getId(): ?IDValue
    {
        return $this->Id;
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        return new static(
            EncryptionProperty::getChildrenOfClass($xml),
            self::getOptionalAttribute($xml, 'Id', IDValue::class, null),
        );
    }


    /**
     * @inheritDoc
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getEncryptionProperty() as $ep) {
            $ep->toXML($e);
        }

        if ($this->getId() !== null) {
            $e->setAttribute('Id', strval($this->getId()));
        }

        return $e;
    }
}
