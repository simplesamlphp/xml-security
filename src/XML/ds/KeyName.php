<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\StringValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a ds:KeyName element.
 *
 * @package simplesamlphp/xml-security
 */
final class KeyName extends AbstractDsElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = StringValue::class;
}
