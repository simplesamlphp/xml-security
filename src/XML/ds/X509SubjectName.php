<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\XMLStringElementTrait;

/**
 * Class representing a ds:X509SubjectName element.
 *
 * @package simplesaml/xml-security
 */
final class X509SubjectName extends AbstractDsElement
{
    use XMLStringElementTrait;
}
