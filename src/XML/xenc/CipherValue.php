<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\Base64StringElementTrait;

/**
 * Class representing a xenc:CipherValue element.
 *
 * @package simplesaml/xml-security
 */
final class CipherValue extends AbstractXencElement
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
