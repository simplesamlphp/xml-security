<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a ds:PGPKeyID element.
 *
 * @package simplesaml/xml-security
 */
final class PGPKeyID extends AbstractDsElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = Base64BinaryValue::class;
}
