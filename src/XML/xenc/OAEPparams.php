<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\Type\Base64BinaryValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a xenc:OAEPparams element.
 *
 * @package simplesaml/xml-security
 */
final class OAEPparams extends AbstractXencElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = Base64BinaryValue::class;
}
