<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\XML\Type\AnyURIValue;

use function strval;

/**
 * Abstract class representing a dsig11:NamedCurveType
 *
 * @package simplesaml/xml-security
 */
abstract class AbstractNamedCurveType extends AbstractDsig11Element
{
    /**
     * Initialize a NamedCurveType element.
     *
     * @param \SimpleSAML\XML\Type\AnyURIValue $URI
     */
    public function __construct(
        protected AnyURIValue $URI,
    ) {
    }


    /**
     * Collect the value of the URI-property
     *
     * @return \SimpleSAML\XML\Type\AnyURIValue
     */
    public function getURI(): AnyURIValue
    {
        return $this->URI;
    }


    /**
     * Convert this NamedCurveType element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this NamedCurveType element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('URI', strval($this->getURI()));

        return $e;
    }
}
