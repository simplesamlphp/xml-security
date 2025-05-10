<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Type\IntegerValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a ds:X509SerialNumber element.
 *
 * @package simplesaml/xml-security
 */
final class X509SerialNumber extends AbstractDsElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = IntegerValue::class;
}
