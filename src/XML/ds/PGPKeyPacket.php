<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSchema\Type\Builtin\Base64BinaryValue;

/**
 * Class representing a ds:PGPKeyPacket element.
 *
 * @package simplesaml/xml-security
 */
final class PGPKeyPacket extends AbstractDsElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = Base64BinaryValue::class;
}
