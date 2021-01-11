<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XMLSecurity\XMLSecurityDSig;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractDsElement extends AbstractXMLElement
{
    /** @var string */
    public const NS = XMLSecurityDSig::XMLDSIGNS;

    /** @var string */
    public const NS_PREFIX = 'ds';


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
}
