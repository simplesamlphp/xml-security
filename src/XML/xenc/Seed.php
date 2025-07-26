<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;

/**
 * Class representing a xenc:seed element.
 *
 * @package simplesaml/xml-security
 */
final class Seed extends AbstractXencElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = CryptoBinaryValue::class;

    /** @var string */
    public const LOCALNAME = 'seed';
}
