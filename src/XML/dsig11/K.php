<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\Type\PositiveIntegerValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a dsig11:K element.
 *
 * @package simplesaml/xml-security
 */
final class K extends AbstractDsig11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = PositiveIntegerValue::class;
}
