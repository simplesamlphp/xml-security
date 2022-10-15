<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Base64ElementTrait;

/**
 * Class representing a ds:Modulus element.
 *
 * @package simplesaml/xml-security
 * @psalm-suppress PropertyNotSetInConstructor $content
 */
final class Modulus extends AbstractDsElement
{
    use Base64ElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
