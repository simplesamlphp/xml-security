<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use SimpleSAML\XMLSecurity\XML\AbstractSignedXMLElement;
use SimpleSAML\XMLSecurity\XML\SignedElementTrait;

/**
 * @package simplesamlphp\saml2
 */
final class CustomSigned extends AbstractSignedXMLElement
{
    /** @var string */
    public const NS = 'urn:ssp:custom';

    /** @var string */
    public const NS_PREFIX = 'custom';


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
