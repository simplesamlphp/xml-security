<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSchema\Type\Builtin\PositiveIntegerValue;

/**
 * Class representing a dsig11:K3 element.
 *
 * @package simplesaml/xml-security
 */
final class K3 extends AbstractDsig11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = PositiveIntegerValue::class;
}
