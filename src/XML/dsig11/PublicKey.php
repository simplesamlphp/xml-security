<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSecurity\Type\ECPointValue;

/**
 * Class representing a dsig11:PublicKey element.
 *
 * @package simplesaml/xml-security
 */
final class PublicKey extends AbstractDsig11Element
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = ECPointValue::class;
}
