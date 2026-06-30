<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use Dom;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;

use function array_last;
use function array_merge;

/**
 * Class representing <xenc11:Salt>.
 *
 * @package simplesamlphp/xml-security
 */
final class Salt extends AbstractXenc11Element
{
    /**
     * Salt constructor.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc11\OtherSource|\SimpleSAML\XMLSecurity\XML\xenc11\Specified $content
     */
    public function __construct(
        protected OtherSource|Specified $content,
    ) {
    }


    /**
     * Get the value of the $content property.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc11\OtherSource|\SimpleSAML\XMLSecurity\XML\xenc11\Specified
     */
    public function getContent(): OtherSource|Specified
    {
        return $this->content;
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(Dom\Element $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        $otherSource = OtherSource::getChildrenOfClass($xml);
        $specified = Specified::getChildrenOfClass($xml);

        $content = array_merge($otherSource, $specified);
        Assert::minCount($content, 1, MissingElementException::class);
        Assert::maxCount($content, 1, TooManyElementsException::class);

        return new static(array_last($content));
    }


    /**
     * @inheritDoc
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);
        $this->getContent()->toXML($e);

        return $e;
    }
}
