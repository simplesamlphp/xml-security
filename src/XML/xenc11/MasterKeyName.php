<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use SimpleSAML\XML\Type\StringValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a xenc11:MasterKeyName element.
 *
 * @package simplesamlphp/xml-security
 */
final class MasterKeyName extends AbstractXenc11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = StringValue::class;
}
