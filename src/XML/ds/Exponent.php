<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;

/**
 * Class representing a ds:Exponent element.
 *
 * @package simplesaml/xml-security
 */
final class Exponent extends AbstractDsElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = CryptoBinaryValue::class;
}
