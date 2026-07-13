<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use Dom;

/**
 * Abstract class representing a dsig11:PrimeFieldParamsType
 *
 * @package simplesaml/xml-security
 */
abstract class AbstractPrimeFieldParamsType extends AbstractDsig11Element
{
    /**
     * Initialize a PrimeFieldParamsType element.
     *
     * @param \SimpleSAML\XMLSecurity\XML\dsig11\P $p
     */
    public function __construct(
        protected P $p,
    ) {
    }


    /**
     * Collect the value of the p-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\dsig11\P
     */
    public function getP(): P
    {
        return $this->p;
    }


    /**
     * Convert this PrimeFieldParamsType element to XML.
     *
     * @param \Dom\Element|null $parent The element we should append this PrimeFieldParamsType element to.
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);
        $this->getP()->toXML($e);

        return $e;
    }
}
