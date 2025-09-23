<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;

/**
 * Class representing a dsig11:B element.
 *
 * @package simplesaml/xml-security
 */
final class B extends AbstractDsig11Element
{
    use TypedTextContentTrait;


    /** @var string */
    public const TEXTCONTENT_TYPE = CryptoBinaryValue::class;
}
