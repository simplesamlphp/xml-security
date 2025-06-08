<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use DOMElement;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XML\XsNamespace as NS;

/**
 * Abstract class representing a dsig11:FieldIDType
 *
 * @package simplesaml/xml-security
 */
abstract class AbstractFieldIDType extends AbstractDsig11Element
{
    use ExtendableElementTrait;

    /** @var \SimpleSAML\XML\XsNamespace */
    public const XS_ANY_ELT_NAMESPACE = NS::OTHER;


    /**
     * Initialize a FieldIDType element.
     *
     * @param \SimpleSAML\XMLSecurity\XML\dsig11\Prime $prime
     * @param \SimpleSAML\XMLSecurity\XML\dsig11\TnB $tnb
     * @param \SimpleSAML\XMLSecurity\XML\dsig11\PnB $pnb
     * @param \SimpleSAML\XMLSecurity\XML\dsig11\GnB $gnb
     * @param array<\SimpleSAML\XML\SerializableElementInterface> $children
     */
    public function __construct(
        protected Prime $prime,
        protected TnB $tnb,
        protected PnB $pnb,
        protected GnB $gnb,
        array $children,
    ) {
        $this->setElements($children);
    }


    /**
     * Collect the value of the prime-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\dsig11\Prime
     */
    public function getPrime(): Prime
    {
        return $this->prime;
    }


    /**
     * Collect the value of the tnb-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\dsig11\TnB
     */
    public function getTnB(): TnB
    {
        return $this->tnb;
    }


    /**
     * Collect the value of the pnb-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\dsig11\PnB
     */
    public function getPnB(): PnB
    {
        return $this->pnb;
    }


    /**
     * Collect the value of the gnb-property
     *
     * @return \SimpleSAML\XMLSecurity\XML\dsig11\GnB
     */
    public function getGnB(): GnB
    {
        return $this->gnb;
    }


    /**
     * Convert this FieldIDType element to XML.
     *
     * @param \DOMElement|null $parent The element we should append this FieldIDType element to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->getPrime()->toXML($e);
        $this->getTnB()->toXML($e);
        $this->getPnB()->toXML($e);
        $this->getGnB()->toXML($e);

        foreach ($this->getElements() as $elt) {
            $elt->toXML($e);
        }

        return $e;
    }
}
