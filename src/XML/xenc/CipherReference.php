<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Constants as C;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, SchemaViolationException};
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSecurity\XML\xenc\Transforms;

use function strval;

/**
 * Class representing a CipherReference.
 *
 * @package simplesamlphp/xml-security
 */
final class CipherReference extends AbstractXencElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * AbstractReference constructor.
     *
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue $uri
     * @param \SimpleSAML\XMLSecurity\XML\xenc\Transforms[] $transforms
     */
    final public function __construct(
        protected AnyURIValue $uri,
        protected array $transforms = [],
    ) {
        Assert::maxCount($transforms, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($transforms, Transforms::class, SchemaViolationException::class);
    }


    /**
     * Get the value of the URI attribute of this reference.
     *
     * @return \SimpleSAML\XMLSchema\Type\AnyURIValue
     */
    public function getURI(): AnyURIValue
    {
        return $this->uri;
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XMLSchema\Exception\MissingAttributeException
     *   if the supplied element is missing one of the mandatory attributes
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getClassName(static::class), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        return new static(
            self::getAttribute($xml, 'URI', AnyURIValue::class),
            Transforms::getChildrenOfClass($xml),
        );
    }


    /**
     * @inheritDoc
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', strval($this->getUri()));

        foreach ($this->transforms as $transforms) {
            $transforms->toXML($e);
        }

        return $e;
    }
}
