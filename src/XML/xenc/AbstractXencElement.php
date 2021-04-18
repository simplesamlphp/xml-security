<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XMLSecurity\Constants;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractXencElement extends AbstractXMLElement
{
    /** @var string */
    public const NS = Constants::XMLENCNS;

    /** @var string */
    public const NS_PREFIX = 'xenc';
}
