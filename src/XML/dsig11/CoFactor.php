<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\Type\IntegerValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a dsig11:CoFactor element.
 *
 * @package simplesaml/xml-security
 */
final class CoFactor extends AbstractDsig11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = IntegerValue::class;
}
