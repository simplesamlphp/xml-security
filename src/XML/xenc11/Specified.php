<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSchema\Type\Builtin\Base64BinaryValue;

/**
 * Class representing a xenc11:Specified element.
 *
 * @package simplesamlphp/xml-security
 */
final class Specified extends AbstractXenc11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = Base64BinaryValue::class;
}
