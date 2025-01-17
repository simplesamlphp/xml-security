<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Type\StringValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a ds:X509SubjectName element.
 *
 * @package simplesaml/xml-security
 */
final class X509SubjectName extends AbstractDsElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = StringValue::class;
}
