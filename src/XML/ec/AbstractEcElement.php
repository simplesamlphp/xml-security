<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ec;

use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XMLSecurity\Constants;


/**
 * Abstract class to be implemented by all the classes in this namespace

 * @package simplesamlphp/xml-security
 */
abstract class AbstractEcElement extends AbstractXMLElement
{
    /** @var string */
    public const NS = Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS;

    /** @var string */
    public const NS_PREFIX = 'ec';


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
     * Get the namespace prefix for the element.
     *
     * @return string
     */
    public static function getNamespacePrefix(): string
    {
        return static::NS_PREFIX;
    }
}