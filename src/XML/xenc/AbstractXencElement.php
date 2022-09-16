<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XMLSecurity\Constants as C;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractXencElement extends AbstractElement
{
    /** @var string */
    public const NS = C::NS_XENC;

    /** @var string */
    public const NS_PREFIX = 'xenc';
}
