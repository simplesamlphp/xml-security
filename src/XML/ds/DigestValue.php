<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Base64StringElementTrait;

/**
 * Class representing a ds:DigestValue element.
 *
 * @package simplesaml/xml-security
 */
final class DigestValue extends AbstractDsElement
{
    use Base64StringElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
