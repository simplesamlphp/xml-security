<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ec;

use DOMElement;
use Webmozart\Assert\Assert;

/**
 * Class implementing InclusiveNamespaces
 *
 * @package simplesamlphp/xml-security
 */
class InclusiveNamespaces extends AbstractEcElement
{
    /** @var string[] */
    protected array $prefixes;


    /**
     * Initialize the InclusiveNamespaces element.
     *
     * @param string[] $prefixes
     */
    public function __construct(array $prefixes)
    {
        $this->setPrefixes($prefixes);
    }


    /**
     * Get the prefixes specified by this element.
     *
     * @return string[]
     */
    public function getPrefixes(): array
    {
        return $this->prefixes;
    }


    /**
     * Set the prefixes to specify in this element.
     *
     * @param string[] $prefixes
     */
    private function setPrefixes(array $prefixes): void
    {
        Assert::allString($prefixes, 'Can only add string InclusiveNamespaces prefixes.');
        $this->prefixes = $prefixes;
    }


    /**
     * Convert XML into an InclusiveNamespaces element.
     *
     * @param \DOMElement $xml The XML element we should load.
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        $prefixes = self::getAttribute($xml, 'PrefixList', '');

        return new self(explode(' ', $prefixes));
    }

    /**
     * Convert this InclusiveNamespaces to XML.
     *
     * @param \DOMElement|null $parent The element we should append this InclusiveNamespaces to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if (!empty($this->prefixes)) {
            $e->setAttribute('PrefixList', join(' ', $this->prefixes));
        }

        return $e;
    }
}