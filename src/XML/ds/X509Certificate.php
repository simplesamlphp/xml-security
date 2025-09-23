<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;

/**
 * Class representing a ds:X509Certificate element.
 *
 * @package simplesamlphp/xml-security
 */
final class X509Certificate extends AbstractDsElement
{
    use TypedTextContentTrait;


    /** @var string */
    public const TEXTCONTENT_TYPE = Base64BinaryValue::class;
}
